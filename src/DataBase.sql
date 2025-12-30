CREATE TABLE Usuario
(
    cod_usu INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    Passwd VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'usuario'
);


CREATE TABLE Equipo
(
    cod_equi INT AUTO_INCREMENT PRIMARY KEY,
    Escudo VARCHAR(500) NOT NULL,
    Nombre VARCHAR(255) NOT NULL,
    cod_usu INT NOT NULL,
    FOREIGN KEY (cod_usu) REFERENCES Usuario(cod_usu) ON DELETE CASCADE
);

CREATE TABLE Jugador
(
    cod_jug INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(255) NOT NULL,
    Apellido VARCHAR(255) NOT NULL,
    Dorsal INT NOT NULL,
    Posicion VARCHAR(255) NOT NULL,
    Imagen VARCHAR(500) NOT NULL,
    Elemento VARCHAR(20),
    cod_equi INT,
    FOREIGN KEY (cod_equi) REFERENCES Equipo(cod_equi) ON DELETE SET NULL
);


-- Insertar al usuario administrador

INSERT INTO Usuario (cod_usu, Nombre, email, Passwd, rol)
VALUES (
    1,
    'Administrador',
    'admin@admin.com',
    '$2y$12$3ifquwEYQbO7TK9zD3DNa.LY.essJYyZdSRhdNMohTVDxRatIupjm',
    'admin'
);

-- DATOS DE PRUEBA

INSERT INTO Usuario (cod_usu, Nombre, email, Passwd, rol) VALUES
(2,'Carlos Pérez', 'carlos@example.com', '$2y$12$XfiR0rmuxUBnnpal6Z0cNumHDVmHTlyak2CM.lb6QuLMVPBX.9Swy', 'usuario'),
(3,'Lucía Gómez', 'lucia@example.com', '$2y$12$XfiR0rmuxUBnnpal6Z0cNumHDVmHTlyak2CM.lb6QuLMVPBX.9Swy', 'usuario'),
(4,'Miguel Torres', 'miguel@example.com', '$2y$12$XfiR0rmuxUBnnpal6Z0cNumHDVmHTlyak2CM.lb6QuLMVPBX.9Swy', 'usuario'),
(5,'Ana Ruiz', 'ana@example.com', '$2y$12$XfiR0rmuxUBnnpal6Z0cNumHDVmHTlyak2CM.lb6QuLMVPBX.9Swy', 'usuario'),
(6,'Javier López', 'javier@example.com', '$2y$12$XfiR0rmuxUBnnpal6Z0cNumHDVmHTlyak2CM.lb6QuLMVPBX.9Swy', 'usuario');

-- JUGADORES DE PRUEBA

INSERT INTO Jugador (Nombre, Apellido, Dorsal, Posicion, Imagen, Elemento, cod_equi) VALUES
('Axel', 'Blaze', 9, 'DL', 'https://i.pinimg.com/originals/1a/2b/3c/1a2b3c4d5e6f7g8h9i0j.jpg', 'FUEGO', NULL),
('Nathan', 'Swift', 7, 'DL', 'https://i.pinimg.com/originals/2b/3c/4d/2b3c4d5e6f7g8h9i0j1k.jpg', 'AIRE', NULL),
('Cliff', 'Edge', 5, 'DF', 'https://i.pinimg.com/originals/3c/4d/5e/3c4d5e6f7g8h9i0j1k2l.jpg', 'MONTANIA', NULL),
('Forest', 'Green', 6, 'MC', 'https://i.pinimg.com/originals/4d/5e/6f/4d5e6f7g8h9i0j1k2l3m.jpg', 'BOSQUE', NULL),
('Jude', 'Sharp', 10, 'MC', 'https://i.pinimg.com/originals/5e/6f/7g/5e6f7g8h9i0j1k2l3m4n.jpg', 'AIRE', NULL),
('Kevin', 'Dragonfly', 11, 'DL', 'https://i.pinimg.com/originals/6f/7g/8h/6f7g8h9i0j1k2l3m4n5o.jpg', 'FUEGO', NULL),
('Rocky', 'Stone', 4, 'DF', 'https://i.pinimg.com/originals/7g/8h/9i/7g8h9i0j1k2l3m4n5o6p.jpg', 'MONTANIA', NULL),
('Leaf', 'Walker', 8, 'MC', 'https://i.pinimg.com/originals/8h/9i/0j/8h9i0j1k2l3m4n5o6p7q.jpg', 'BOSQUE', NULL),
('Gale', 'Storm', 2, 'PT', 'https://i.pinimg.com/originals/9i/0j/1k/9i0j1k2l3m4n5o6p7q8r.jpg', 'AIRE', NULL),
('Bryce', 'Flare', 3, 'DL', 'https://i.pinimg.com/originals/0j/1k/2l/0j1k2l3m4n5o6p7q8r9s.jpg', 'FUEGO', NULL);

INSERT INTO Equipo (Escudo, Nombre, cod_usu) VALUES
('a', 'Equipo 1', 1),
('a', 'Equipo 2', 2),
('a', 'Equipo 3', 3),
('a', 'Equipo 4', 4),
('a', 'Equipo 5', 5),
('a', 'Equipo 6', 6);
