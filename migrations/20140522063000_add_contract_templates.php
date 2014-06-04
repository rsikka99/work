<?php

use Phinx\Migration\AbstractMigration;

class AddContractTemplates extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $contract_templates = $this->table('contract_templates');
        $contract_templates->addColumn('dealerId', 'integer')
                           ->addColumn('templateName', 'string', array('default' => false))
                           ->addColumn('isSystemTemplate', 'boolean')
                           ->addForeignKey(array('dealerId'), 'dealers', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
                           ->save();

        $contract_sections = $this->table('contract_sections');
        $contract_sections->addColumn('sectionDefaultName', 'string')
                          ->addColumn('sectionDefaultText', 'text')
                          ->save();

        $contract_template_sections = $this->table('contract_template_sections', array('id' => false, 'primary_key' => array('contractTemplateId', 'contractSectionId')));
        $contract_template_sections->addColumn('contractTemplateId', 'integer')
                                   ->addColumn('contractSectionId', 'integer')
                                   ->addColumn('enabled', 'boolean', array('default' => true))
                                   ->addColumn('sectionName', 'string')
                                   ->addColumn('sectionText', 'text')
                                   ->addForeignKey(array('contractTemplateId'), $contract_templates, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
                                   ->addForeignKey(array('contractSectionId'), $contract_sections, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
                                   ->save();

        $this->execute('INSERT INTO contract_sections (id, sectionDefaultName, sectionDefaultText) VALUES
    (1, \'Customer ("you" or "your")\', \'\'),
    (2, "Vendor (Vendor is not Owner\'s agent nor is Vendor authorized to waive or alter any term or condition of this Agreement)", \'\'),
    (3, \'Contract\', \'<p><b>This agreement is non-cancelable and irrevocable. It cannot be terminated. please read carefully before signing. This agreement and
any claim related to this agreement shall be governed by the laws of the state of Iowa. Any dispute will be adjudicated in a
federal or state court in Linn County, Iowa. You hereby consent to personal jurisdiction and venue in such courts and waive
transfer of venue. Each party waives any right to a jury trial.</b></p>\'),
    (4, "Customer\'s Authorized Signature", \'<p><b>By signing this page, you represent to owner that you have received and read the additional terms and conditions appearing on the
second page of this two-page agreement. This agreement is binding when owner pays for the equipment.</b></p>\'),
    (5, \'Owner ("we", "us", "our")\', \'\'),
    (6, \'Unconditional Guaranty\', \'<p>The undersigned, jointly and severally if more than one, unconditionally guarantee(s) that the Customer will timely perform all obligations under the Agreement. The undersigned also waive(s) any notification if the Customer is in default and consent(s) to any extensions or modifications granted to the Customer. In the event of default, the undersigned will immediately pay all sums due under the terms of the Agreement without requiring Owner to proceed against Customer or any other party or exercise any rights in the Equipment. The undersigned, as to this guaranty, agree(s) to the designated forum and consent(s) to personal jurisdiction, venue, and choice of law as stated in the Agreement, agree(s) to pay all costs and expenses, including attorney fees, incurred by Owner related to this guaranty and the Agreement, waive(s) a jury trial and transfer of venue, and authorize(s) obtaining credit reports.</p>\'),
    (7, \'MPS Contract Details\', \'\'),
    (8, \'Hardware Contract Details\', \'\'),
    (9, \'Description Of Our Devices\', \'\'),
    (10, \'Additional Terms & Conditions\', \'<p><b>MISCELLANEOUS.</b> This Agreement is the entire agreement between you and us and supersedes any prior representations or agreements, including any purchase orders.
Amounts payable under this Agreement may include a profit to us. The original of this Agreement shall be that copy which bears your facsimile or original signature, and which
bears our original signature. If a court finds any provision of this Agreement unenforceable, the remaining terms of this Agreement shall remain in effect. You authorize us to
either insert or correct the Agreement number, serial numbers, model numbers, beginning date, and signature date. All other modifications to the Agreement must be in writing
signed by each party.</p>\');');

        $this->execute('INSERT INTO contract_templates (id, dealerId, templateName, isSystemTemplate) VALUES
    (1, 1, \'Default Hardware Contract\', TRUE),
    (2, 1, \'Default Combined Hardware And MPS Contract\', TRUE);');

        $this->execute('INSERT INTO contract_template_sections (contractTemplateId, contractSectionId, enabled, sectionName, sectionText) VALUES
    (1, 1, TRUE, \'\', \'\'),
    (1, 2, TRUE, \'\', \'\'),
    (1, 3, TRUE, \'\', \'\'),
    (1, 4, TRUE, \'\', \'\'),
    (1, 5, TRUE, \'\', \'\'),
    (1, 6, TRUE, \'\', \'\'),
    (1, 8, TRUE, \'\', \'\'),
    (1, 9, TRUE, \'\', \'\'),
    (1, 10, TRUE, \'\', \'\');');

        $this->execute('INSERT INTO contract_template_sections (contractTemplateId, contractSectionId, enabled, sectionName, sectionText) VALUES
    (2, 1, TRUE, \'\', \'\'),
    (2, 2, TRUE, \'\', \'\'),
    (2, 3, TRUE, \'\', \'\'),
    (2, 4, TRUE, \'\', \'\'),
    (2, 5, TRUE, \'\', \'\'),
    (2, 6, TRUE, \'\', \'\'),
    (2, 7, TRUE, \'\', \'\'),
    (2, 9, TRUE, \'\', \'\'),
    (2, 10, TRUE, \'\', \'\');');

    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->dropTable('contract_template_sections');
        $this->dropTable('contract_sections');
        $this->dropTable('contract_templates');
    }
}