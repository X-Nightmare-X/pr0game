-- add table advanced_stats

CREATE TABLE `%PREFIX%advanced_stats` (
  `userId` int(10) unsigned NOT NULL,

  `build_202` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_203` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_204` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_205` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_206` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_207` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_208` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_209` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_210` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_211` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_212` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_213` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_214` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_215` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_216` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_217` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_218` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_219` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_401` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_402` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_403` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_404` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_405` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_406` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_407` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_408` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_409` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_410` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_411` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_502` bigint(20) unsigned NOT NULL DEFAULT '0',
  `build_503` bigint(20) unsigned NOT NULL DEFAULT '0',

  `lost_202` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_203` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_204` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_205` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_206` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_207` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_208` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_209` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_210` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_211` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_212` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_213` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_214` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_215` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_216` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_217` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_218` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_219` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_401` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_402` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_403` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_404` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_405` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_406` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_407` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_408` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_409` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_410` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_411` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_502` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lost_503` bigint(20) unsigned NOT NULL DEFAULT '0',

  `repaired_401` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_402` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_403` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_404` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_405` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_406` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_407` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_408` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_409` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_410` bigint(20) unsigned NOT NULL DEFAULT '0',
  `repaired_411` bigint(20) unsigned NOT NULL DEFAULT '0',
  
  `destroyed_202` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_203` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_204` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_205` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_206` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_207` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_208` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_209` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_210` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_211` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_212` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_213` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_214` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_215` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_216` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_217` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_218` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_219` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_401` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_402` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_403` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_404` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_405` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_406` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_407` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_408` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_409` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_410` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_411` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_502` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destroyed_503` bigint(20) unsigned NOT NULL DEFAULT '0',

  `found_202` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_203` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_204` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_205` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_206` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_207` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_208` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_209` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_210` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_211` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_212` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_213` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_214` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_215` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_216` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_217` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_218` bigint(20) unsigned NOT NULL DEFAULT '0',
  `found_219` bigint(20) unsigned NOT NULL DEFAULT '0',
  
  `found_901` double(50,0) unsigned NOT NULL DEFAULT '0',
  `found_902` double(50,0) unsigned NOT NULL DEFAULT '0',
  `found_903` double(50,0) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO `%PREFIX%advanced_stats` (`userId`) SELECT `id` FROM `%PREFIX%users`;