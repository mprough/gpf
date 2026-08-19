DELETE FROM configuration WHERE configuration_key LIKE 'GPSF_%';
DELETE FROM configuration_group WHERE configuration_group_title = 'Red Headed Stepchild of Zen Cart® Google Product Search Feeder II';
DELETE FROM admin_pages WHERE page_key IN('configGpsf', 'toolGpsf');