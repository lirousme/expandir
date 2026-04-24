CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE elementos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_do_elemento VARCHAR(255) NOT NULL,
    id_usuario INT UNSIGNED,
    pessoal INT,

    CONSTRAINT fk_elemento_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE informacoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    texto_ptbr TEXT,
    texto_engb TEXT,
    audio_ptbr TEXT,
    audio_engb TEXT,
    nivel INT,
    pessoal INT,
    id_usuario INT UNSIGNED,

    CONSTRAINT fk_info_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE combinacoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_info_um INT UNSIGNED,
    id_info_dois INT UNSIGNED,
    id_info_tres INT UNSIGNED,
    id_usuario INT UNSIGNED,
    revisoes INT,

    CONSTRAINT fk_info_um
        FOREIGN KEY (id_info_um)
        REFERENCES informacoes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_info_dois
        FOREIGN KEY (id_info_dois)
        REFERENCES informacoes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_info_tres
        FOREIGN KEY (id_info_tres)
        REFERENCES informacoes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_combinacao_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE elementos_informacoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_elemento INT UNSIGNED,
    id_informacao INT UNSIGNED,
    id_usuario INT UNSIGNED,

    CONSTRAINT fk_elemento
        FOREIGN KEY (id_elemento)
        REFERENCES elementos(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_informacao
        FOREIGN KEY (id_informacao)
        REFERENCES informacoes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE biblioteca (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED,
    id_elemento INT UNSIGNED,
    ordem INT,

    CONSTRAINT fk_biblioteca_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_biblioteca_elemento
        FOREIGN KEY (id_elemento)
        REFERENCES elementos(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

