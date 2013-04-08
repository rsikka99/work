-- UPDATE pgen_toners SET cost=ROUND(cost * .9, 1);

/*
 * This calculation will inflate prices by a random factor.
 * The margin will be between 3.5% and 5.5%. After applying
 * the margin it will round to the nearest .25
*/
SELECT
    cost                                                                   AS originalCost,
    (ROUND(cost / (1 - ((5.5 - RAND() * 2) / 100)) * 100 / 25) * 25 / 100) AS newCost
FROM pgen_toners;
