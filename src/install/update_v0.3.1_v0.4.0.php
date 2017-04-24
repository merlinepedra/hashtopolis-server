<?php
/**
 * Created by IntelliJ IDEA.
 * User: sein
 * Date: 06.03.17
 * Time: 12:16
 */

require_once(dirname(__FILE__) . "/../../inc/load.php");

echo "Apply updates...\n";

echo "Create StatsGen table...\n";
$FACTORIES::getAgentFactory()->getDB()->query("CREATE TABLE `HashlistStats` (`hashlistStatsId` INT(11) NOT NULL, `hashlistId` INT(11) NOT NULL, `data` TEXT COLLATE utf8_unicode_ci NOT NULL) ENGINE=InnoDB;");
echo "  Add indizes...\n";
$FACTORIES::getAgentFactory()->getDB()->query("ALTER TABLE `HashlistStats` ADD PRIMARY KEY (`hashlistStatsId`), ADD UNIQUE KEY `hashlistId` (`hashlistId`);");
echo "  Add auto increment...\n";
$FACTORIES::getAgentFactory()->getDB()->query("ALTER TABLE `HashlistStats` MODIFY `hashlistStatsId` INT(11) NOT NULL AUTO_INCREMENT;");
echo "  Add constraints...\n";
$FACTORIES::getAgentFactory()->getDB()->query("ALTER TABLE `HashlistStats` ADD CONSTRAINT FOREIGN KEY (`hashlistId`) REFERENCES Hashlist(hashlistId);");
echo "OK\n";

echo "Update complete!\n";
