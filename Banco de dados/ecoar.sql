CREATE DATABASE IF NOT EXISTS db_repertorio;
USE db_repertorio;

-- Tabela de Artistas
CREATE TABLE tb_artistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- Tabela de Estilos
CREATE TABLE tb_estilos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(50) NOT NULL
);

-- Tabela de Momentos
CREATE TABLE tb_momentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(100) NOT NULL
);

-- Tabela Principal de Músicas
CREATE TABLE tb_musicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    link VARCHAR(255),
    artista_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_musica_artista FOREIGN KEY (artista_id) REFERENCES tb_artistas(id)
);

-- Tabela Pivô: Música <-> Estilos
CREATE TABLE tb_musica_estilos (
    musica_id INT,
    estilo_id INT,
    PRIMARY KEY (musica_id, estilo_id),
    CONSTRAINT fk_me_musica FOREIGN KEY (musica_id) REFERENCES tb_musicas(id) ON DELETE CASCADE,
    CONSTRAINT fk_me_estilo FOREIGN KEY (estilo_id) REFERENCES tb_estilos(id)
);

-- Tabela Pivô: Música <-> Momentos
CREATE TABLE tb_musica_momentos (
    musica_id INT,
    momento_id INT,
    PRIMARY KEY (musica_id, momento_id),
    CONSTRAINT fk_mm_musica FOREIGN KEY (musica_id) REFERENCES tb_musicas(id) ON DELETE CASCADE,
    CONSTRAINT fk_mm_momento FOREIGN KEY (momento_id) REFERENCES tb_momentos(id)
);