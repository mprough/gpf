DELETE FROM configuration WHERE configuration_key LIKE 'GPSF_%' OR configuration_key = 'RHS_GPSF_VERSION';
DELETE FROM configuration_group WHERE configuration_group_title IN ('Google Product Search Feeder II', 'Red Headed Stepchild of Zen Cart® Google Product Search Feeder II');
DELETE FROM admin_pages WHERE page_key IN('configGpsf', 'toolGpsf');
