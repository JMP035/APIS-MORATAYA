CREATE DATABASE SCIFI


CREATE TABLE favoritos (
    fav_id SERIAL PRIMARY KEY,
    fav_item VARCHAR(100),
    fav_api VARCHAR(20),
    fav_tipo VARCHAR(30),
    fav_titulo varchar (50),
    fav_fecha DATE DEFAULT TODAY
)

drop table favoritos



CREATE TABLE informix.mascotas  ( 
	mascota_id	SERIAL NOT NULL,
	nombre    	VARCHAR(50) NOT NULL,
	tipo      	VARCHAR(50) NOT NULL,
	raza      	VARCHAR(50) NOT NULL,
	edad      	INTEGER NOT NULL,
	sexo      	VARCHAR(1) NOT NULL,
	dueno_id  	INTEGER NOT NULL,
	situacion 	SMALLINT DEFAULT 1,
	PRIMARY KEY(mascota_id)
	ENABLED
)
LOCK MODE ROW
GO
ALTER TABLE informix.mascotas
	ADD CONSTRAINT ( FOREIGN KEY(dueno_id)
	REFERENCES informix.duenos(dueno_id) CONSTRAINT r107_66
	ENABLED )
GO
