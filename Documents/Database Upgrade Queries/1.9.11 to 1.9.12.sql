Alter table Settings ADD  `Program_Enable_KeyHooks` int(11) NOT NULL; 
Update Settings set `Program_Enable_KeyHooks` = 1;
Alter table Files ADD  `lastWrite` datetime NOT NULL;
Update Files set `lastWrite` = Uploaded;