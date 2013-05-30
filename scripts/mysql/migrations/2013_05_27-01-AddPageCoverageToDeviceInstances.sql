ALTER TABLE `device_instances`
ADD COLUMN `pageCoverageMonochrome` DOUBLE DEFAULT 5,
ADD COLUMN `pageCoverageCyan` DOUBLE DEFAULT 5,
ADD COLUMN `pageCoverageMagenta` DOUBLE DEFAULT 5,
ADD COLUMN `pageCoverageYellow` DOUBLE DEFAULT 5;