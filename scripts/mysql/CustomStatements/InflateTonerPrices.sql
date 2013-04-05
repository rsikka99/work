-- UPDATE pgen_toners SET cost=ROUND(cost * .9, 1);
SELECT
    cost as originalCost,
    (ROUND(cost / .95 * 100 / 25) * 25 / 100) as newCost
FROM pgen_toners;
