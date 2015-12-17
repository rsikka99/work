<?php

use Phinx\Migration\AbstractMigration;

class CheapestToners extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('DROP PROCEDURE if exists getCheapestTonersForDevice');

        $this->execute('drop view if exists _view_dealer_toner_costs');
        $this->execute('create view _view_dealer_toner_costs as
SELECT dealerId, tonerId, cost, 1 as isUsingDealerPricing
	FROM dealer_toner_attributes
	where cost is not null
union
select dealerId, tonerId, customer_price as cost, 2 as isUsingDealerPricing
	from ingram_prices
	join ingram_products on ingram_prices.ingram_part_number = ingram_products.ingram_part_number
	where ingram_products.tonerId is not null');

        $this->execute('drop view if exists _view_toners_and_dealers');
        $this->execute('create view _view_toners_and_dealers as select toners.id as tonerId, toners.cost, dealers.id as dealerId from toners, dealers');

        $this->execute('drop view if exists _view_dealer_toner_min_costs');
        $this->execute('create view _view_dealer_toner_min_costs as select tonerId, dealerId, min(cost) as cost,isUsingDealerPricing from _view_dealer_toner_costs group by tonerId, dealerId');

        $this->execute('drop view if exists _view_cheapest_toner_cost');
        $this->execute('create view _view_cheapest_toner_cost as
select tonersAndDealers.dealerId, tonersAndDealers.tonerId, COALESCE(dealer_costs.cost,tonersAndDealers.cost) as cost,COALESCE(dealer_costs.isUsingDealerPricing, 0) as isUsingDealerPricing
from _view_toners_and_dealers as tonersAndDealers
left join _view_dealer_toner_min_costs as dealer_costs on dealer_costs.tonerId=tonersAndDealers.tonerId and dealer_costs.dealerId=tonersAndDealers.dealerId
');
        for ($i=1;$i<10;$i++) {
            $this->execute('drop view if exists _view_level'.$i.'_toner_cost');
            $this->execute('create view _view_level'.$i.'_toner_cost as
select tonersAndDealers.dealerId, tonersAndDealers.tonerId, COALESCE(dealer_toner_attributes.level'.$i.',dealer_toner_attributes.cost,tonersAndDealers.cost) as cost,IF(dealer_toner_attributes.level'.$i.' IS NULL, 0, 1) as isUsingDealerPricing
from _view_toners_and_dealers as tonersAndDealers
left join dealer_toner_attributes as dealer_toner_attributes on dealer_toner_attributes.tonerId=tonersAndDealers.tonerId and dealer_toner_attributes.dealerId=tonersAndDealers.dealerId
');
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('drop view if exists _view_dealer_toner_costs;');
        $this->execute('drop view if exists _view_toners_and_dealers;');
        $this->execute('drop view if exists _view_dealer_toner_min_costs;');
        $this->execute('drop view if exists _view_cheapest_toner_cost;');
        for ($i=1;$i<10;$i++) {
            $this->execute('drop view if exists _view_level' . $i . '_toner_cost');
        }

        $this->execute('DROP PROCEDURE if exists getCheapestTonersForDevice');
        $this->execute(
<<<SQL
CREATE PROCEDURE `getCheapestTonersForDevice`(IN inMasterDeviceId       INT(11), IN inDealerId INT(11), IN inMonochromeTonerPreference TEXT, IN inColorTonerPreference TEXT, IN inClientId INT(11))
BEGIN
        SET inMonochromeTonerPreference = IF(CHAR_LENGTH(inMonochromeTonerPreference) > 0, CONCAT(inMonochromeTonerPreference, ','), inMonochromeTonerPreference);
        SET inColorTonerPreference = IF(CHAR_LENGTH(inColorTonerPreference) > 0, CONCAT(inColorTonerPreference, ','), inColorTonerPreference);
        SET inClientId = IF(CHAR_LENGTH(inClientId) > 0, inClientId, 0);

        SELECT
            *
        FROM (
                 SELECT
                     device_toners.toner_id                                                                         AS id,
                     toners.sku,
                     toners.cost,
                     COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost)                  AS calculatedCost,
                     IF(client_toner_orders.cost IS NOT NULL, TRUE, FALSE)                                          AS isUsingCustomerPricing,
                     IF(client_toner_orders.cost IS NULL AND dealer_toner_attributes.cost IS NOT NULL, TRUE, FALSE) AS isUsingDealerPricing,
                     toners.yield,
                     COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost) / toners.yield   AS costPerPage,
                     toners.manufacturerId,
                     IF(master_devices.manufacturerId = toners.manufacturerId, 1, 0)                                AS isOem,
                     toners.tonerColorId
                 FROM device_toners
                     LEFT JOIN toners ON toners.id = device_toners.toner_id
                     LEFT JOIN master_devices ON master_devices.id = device_toners.master_device_id
                     LEFT JOIN dealer_toner_attributes
                         ON dealer_toner_attributes.tonerId = device_toners.toner_id AND dealer_toner_attributes.dealerId = inDealerId
                     LEFT JOIN client_toner_orders
                         ON client_toner_orders.tonerId = device_toners.toner_id AND client_toner_orders.clientId = inClientId
                 WHERE device_toners.master_device_id = inMasterDeviceId AND toners.tonerColorId != 1
                       AND find_in_set(toners.manufacturerId, CONCAT(inColorTonerPreference, master_devices.manufacturerId))
                       AND COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost) / toners.yield =
                           (SELECT
                                min(COALESCE(cta.cost, dta.cost, t.cost) / t.yield)
                            FROM device_toners
                                LEFT JOIN toners AS t
                                    ON t.id = device_toners.toner_id
                                LEFT JOIN
                                master_devices AS md
                                    ON md.id = device_toners.master_device_id
                                LEFT JOIN dealer_toner_attributes AS dta
                                    ON dta.tonerId = device_toners.toner_id AND dta.dealerId = inDealerId
                                LEFT JOIN client_toner_orders AS cta
                                    ON cta.tonerId = device_toners.toner_id AND cta.clientId = inClientId
                            WHERE
                                toners.tonerColorId = t.tonerColorId AND
                                device_toners.master_device_id = inMasterDeviceId AND
                                t.manufacturerId = toners.manufacturerId)
                 ORDER BY find_in_set(toners.manufacturerId, CONCAT(inColorTonerPreference, master_devices.manufacturerId)),
                     COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost) / toners.yield ASC
             ) AS selectStatement1
        GROUP BY selectStatement1.tonerColorId

        UNION

        SELECT
            *
        FROM (
                 SELECT
                     device_toners.toner_id                                                                         AS id,
                     toners.sku,
                     toners.cost,
                     COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost)                  AS calculatedCost,
                     IF(client_toner_orders.cost IS NOT NULL, TRUE, FALSE)                                          AS isUsingCustomerPricing,
                     IF(client_toner_orders.cost IS NULL AND dealer_toner_attributes.cost IS NOT NULL, TRUE, FALSE) AS isUsingDealerPricing,
                     toners.yield,
                     COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost) / toners.yield   AS costPerPage,
                     toners.manufacturerId,
                     IF(master_devices.manufacturerId = toners.manufacturerId, 1, 0)                                AS isOem,
                     toners.tonerColorId
                 FROM device_toners
                     LEFT JOIN toners ON toners.id = device_toners.toner_id
                     LEFT JOIN master_devices ON master_devices.id = device_toners.master_device_id
                     LEFT JOIN dealer_toner_attributes
                         ON dealer_toner_attributes.tonerId = device_toners.toner_id AND dealer_toner_attributes.dealerId = inDealerId
                     LEFT JOIN client_toner_orders
                         ON client_toner_orders.tonerId = device_toners.toner_id AND client_toner_orders.clientId = inClientId
                 WHERE device_toners.master_device_id = inMasterDeviceId AND toners.tonerColorId = 1
                       AND find_in_set(toners.manufacturerId, CONCAT(inMonochromeTonerPreference, master_devices.manufacturerId))
                       AND COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost) / toners.yield =
                           (SELECT
                                min(COALESCE(cta.cost, dta.cost, t.cost) / t.yield)
                            FROM device_toners
                                LEFT JOIN toners AS t
                                    ON t.id = device_toners.toner_id
                                LEFT JOIN
                                master_devices AS md
                                    ON md.id = device_toners.master_device_id
                                LEFT JOIN dealer_toner_attributes AS dta
                                    ON dta.tonerId = device_toners.toner_id AND dta.dealerId = inDealerId
                                LEFT JOIN client_toner_orders AS cta
                                    ON cta.tonerId = device_toners.toner_id AND cta.clientId = inClientId
                            WHERE
                                toners.tonerColorId = t.tonerColorId AND
                                device_toners.master_device_id = inMasterDeviceId AND
                                t.manufacturerId = toners.manufacturerId)
                 ORDER BY find_in_set(toners.manufacturerId, CONCAT(inMonochromeTonerPreference, master_devices.manufacturerId)),
                     COALESCE(client_toner_orders.cost, dealer_toner_attributes.cost, toners.cost) / toners.yield ASC
             ) AS selectStatement2
        GROUP BY selectStatement2.tonerColorId;

    END
SQL

        );
    }
}