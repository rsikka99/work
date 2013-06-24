-- -----------------------------------------------------
-- Table `hardware_optimization_quotes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hardware_optimization_quotes` (
    `hardwareOptimizationId` INT(11) NOT NULL,
    `quoteId`                INT(11) NOT NULL,
    PRIMARY KEY (`hardwareOptimizationId`, `quoteId`),
    INDEX `hardware_optimization_quotes_ibk1_idx` (`hardwareOptimizationId` ASC),
    INDEX `hardware_optimization_quotes_ibk2_idx` (`quoteId` ASC),
    CONSTRAINT `hardware_optimization_quotes_ibk1`
    FOREIGN KEY (`hardwareOptimizationId`)
    REFERENCES `hardware_optimizations` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_quotes_ibk2`
    FOREIGN KEY (`quoteId`)
    REFERENCES `quotes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE);