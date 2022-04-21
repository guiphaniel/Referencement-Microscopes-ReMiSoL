BEGIN TRANSACTION;
insert into compagny values(1, 'Bruker');
insert into brand values(1, 'Bruker',1);
insert into brand values(2, 'JPK',1);
insert into model values(1, 'multimode',1);
insert into model values(2, 'dimension 3000',1);
insert into model values(3, 'dimension 3100',1);
insert into model values(4, 'Nanowizard',1);
insert into model values(5, 'dimension Icon',1);
insert into model values(6, 'Edge',1);
insert into model values(7, 'dimension XR',1);
insert into model values(8, 'dimension FastScan',1);
insert into model values(9, 'NanoWizard NanoOptics',1);
insert into model values(10, 'NanoWizard 4 XP NanoScience',1);
insert into model values(11, 'dimension Icon Raman',1);
insert into model values(12, 'MultiMode 8 HR',1);
insert into model values(13, 'Innova',1);
insert into keyword values(1,'Environnement','Milieu liquide');
insert into keyword values(2,'Environnement','Ultravide');
insert into keyword values(3,'Environnement','Vide');
insert into keyword values(4,'Environnement','Air');
insert into keyword values(5,'Environnement','Atmosphère contrôlée');
insert into keyword values(6,'Modes','Contact');
insert into keyword values(7,'Modes','Tapping');
insert into keyword values(8,'Modes','Non contact');
insert into keyword values(9,'Modes','Spectroscopie');
insert into keyword values(10,'Modes','Q+');
insert into keyword values(11,'Modes','SCM');
insert into keyword values(12,'Modes','SSRM');
insert into keyword values(13,'Modes','CAFM');
insert into keyword values(14,'Modes','SThM');
insert into keyword values(15,'Modes','KPFM');
insert into keyword values(16,'Modes','EFM');
insert into keyword values(17,'Modes','PFAFM');
insert into keyword values(18,'Modes','FluidFM');
insert into keyword values(19,'Modes','PFM');
insert into keyword values(20,'Modes','Nanoindentation');
insert into keyword values(21,'Modes','Nanolithographie');
insert into keyword values(22,'Modes','Nanomanipulation');
insert into keyword values(23,'Modes','LFM');
insert into keyword values(24,'Modes','MFM');
insert into keyword values(25,'Modes','STM');
insert into keyword values(26,'Matériaux','Oxydes');
insert into keyword values(27,'Matériaux','Métaux');
insert into keyword values(28,'Matériaux','Polymère');
insert into keyword values(29,'Matériaux','Minéraux');
insert into keyword values(30,'Matériaux','Molécule');
insert into keyword values(31,'Matériaux','Graphène');
insert into keyword values(32,'Matériaux','Bois');
insert into keyword values(33,'Matériaux','Semi-conducteur');
insert into keyword values(34,'Matériaux','Organique');
insert into keyword values(35,'Thématiques','Biologie');
insert into keyword values(36,'Thématiques','Matériaux mous');
insert into keyword values(37,'Thématiques','Rhéologie');
insert into keyword values(38,'Thématiques','Magnétisme');
insert into keyword values(39,'Thématiques','Couches minces');
insert into keyword values(40,'Thématiques','Autoorganisation');
insert into keyword values(41,'Thématiques','Basse température');
insert into keyword values(42,'Thématiques','AFM rapide');
insert into keyword values(43,'Thématiques','Batterie');
COMMIT;
