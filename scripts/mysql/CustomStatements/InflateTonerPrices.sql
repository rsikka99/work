-- UPDATE toners SET cost=ROUND(cost / (1 - ((5.5 - RAND() * 2) / 100)));

/*
 * This calculation will inflate prices by a random factor.
 * The margin will be between 3.5% and 5.5%. After applying
 * the margin it will round to the nearest dollar
*/
SELECT
    cost                                             AS originalCost,
    (ROUND(cost / (1 - ((5.5 - RAND() * 2) / 100)))) AS newCost
FROM toners;