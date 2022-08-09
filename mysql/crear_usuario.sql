use ener;

INSERT INTO `en_users`
(`ide`, `usuario`, `email`, `company`, `fullname`, `address`, `ciudad`, `zip`, `aboutme`, `picture`, `idcard`, `perfil`, `up_date`, `active`, `idkey`) 
VALUES 
(NULL,'sergiom','sergio.marquez@gocredit.mx','Gocredit','Sergio Marquez','Zaragoza 1300 Sur','Nuevo Leon','6400','Usario Administrador de la plataforma.','avatar.jpg','4242424242424242','DEVELOP',SUBSTRING(NOW(),1,10),1,'123456');