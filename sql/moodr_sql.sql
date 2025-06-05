CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL
);

CREATE TABLE mood_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    data DATE NOT NULL,
    humor VARCHAR(50) NOT NULL,
    anotacao TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mood_entry_id INT,
    tag VARCHAR(50),
    FOREIGN KEY (mood_entry_id) REFERENCES mood_entries(id) ON DELETE CASCADE
);

CREATE TABLE phrases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL
);
