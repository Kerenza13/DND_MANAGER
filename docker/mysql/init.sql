SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS character_exports;
DROP TABLE IF EXISTS character_sheets;
DROP TABLE IF EXISTS classes;
DROP TABLE IF EXISTS races;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================
-- USERS
-- =========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,

    role ENUM('ROLE_USER', 'ROLE_ADMIN') DEFAULT 'ROLE_USER',
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- RACES
-- =========================
CREATE TABLE races (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,

    stat_bonuses JSON,
    traits JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- CLASSES
-- =========================
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,

    hit_die INT,
    primary_ability VARCHAR(50),

    saving_throws JSON,
    features JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- CHARACTER SHEETS
-- =========================
CREATE TABLE character_sheets (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,

    race_id INT NULL,
    class_id INT NULL,

    level INT DEFAULT 1,
    background VARCHAR(100),
    alignment VARCHAR(50),
    experience INT DEFAULT 0,

    stats JSON NOT NULL,

    inventory JSON,
    items JSON,

    lore TEXT,

    class_snapshot JSON,
    race_snapshot JSON,

    avatar_url VARCHAR(255),
    notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_class (class_id),
    INDEX idx_race (race_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (race_id) REFERENCES races(id) ON DELETE SET NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

-- =========================
-- CHARACTER EXPORTS
-- =========================
CREATE TABLE character_exports (
    id INT AUTO_INCREMENT PRIMARY KEY,

    character_id INT NOT NULL,

    file_path VARCHAR(255),
    type ENUM('pdf', 'json') DEFAULT 'pdf',

    snapshot JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_character (character_id),

    FOREIGN KEY (character_id) REFERENCES character_sheets(id) ON DELETE CASCADE
);


-- =========================
-- DEMO DATA    
-- =========================

-- =========================
-- USERS
-- =========================
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@dnd.com', '$2y$10$examplehash', 'ROLE_ADMIN'),
('player1', 'player1@dnd.com', '$2y$10$examplehash', 'ROLE_USER'),
('player2', 'player2@dnd.com', '$2y$10$examplehash', 'ROLE_USER');

-- =========================
-- RACES
-- =========================
INSERT INTO races (name, description, stat_bonuses, traits) VALUES
(
  'Human',
  'Versatile and ambitious.',
  JSON_OBJECT('all', 1),
  JSON_ARRAY('Extra Language', 'Adaptable')
),
(
  'Elf',
  'Graceful and magical beings.',
  JSON_OBJECT('dexterity', 2),
  JSON_ARRAY('Darkvision', 'Keen Senses', 'Fey Ancestry')
),
(
  'Dwarf',
  'Stout and resilient.',
  JSON_OBJECT('constitution', 2),
  JSON_ARRAY('Darkvision', 'Dwarven Resilience', 'Stonecunning')
),
(
  'Tiefling',
  'Descendants of infernal bloodlines.',
  JSON_OBJECT('charisma', 2, 'intelligence', 1),
  JSON_ARRAY('Darkvision', 'Hellish Resistance', 'Infernal Legacy')
);

-- =========================
-- CLASSES
-- =========================
INSERT INTO classes (name, description, hit_die, primary_ability, saving_throws, features) VALUES
(
  'Fighter',
  'Master of martial combat.',
  10,
  'Strength',
  JSON_ARRAY('Strength', 'Constitution'),
  JSON_ARRAY('Second Wind', 'Action Surge')
),
(
  'Wizard',
  'Scholars of arcane magic.',
  6,
  'Intelligence',
  JSON_ARRAY('Intelligence', 'Wisdom'),
  JSON_ARRAY('Spellcasting', 'Arcane Recovery')
),
(
  'Rogue',
  'Stealthy and dexterous.',
  8,
  'Dexterity',
  JSON_ARRAY('Dexterity', 'Intelligence'),
  JSON_ARRAY('Sneak Attack', 'Cunning Action')
),
(
  'Cleric',
  'Divine spellcasters.',
  8,
  'Wisdom',
  JSON_ARRAY('Wisdom', 'Charisma'),
  JSON_ARRAY('Spellcasting', 'Channel Divinity')
);

-- =========================
-- CHARACTER SHEETS
-- =========================
INSERT INTO character_sheets (
    user_id, name, race_id, class_id, level, background, alignment,
    experience, stats, inventory, items, lore, class_snapshot, race_snapshot
) VALUES
(
  2,
  'Arannis',
  2, -- Elf
  2, -- Wizard
  3,
  'Sage',
  'Neutral Good',
  900,
  JSON_OBJECT('str', 8, 'dex', 14, 'con', 12, 'int', 16, 'wis', 13, 'cha', 10),
  JSON_ARRAY('Spellbook', 'Robe'),
  JSON_ARRAY('Wand', 'Potion of Healing'),
  'A scholar seeking ancient knowledge.',
  JSON_OBJECT('name', 'Wizard', 'hit_die', 6),
  JSON_OBJECT('name', 'Elf', 'dexterity', 2)
),
(
  3,
  'Thorin',
  3, -- Dwarf
  1, -- Fighter
  4,
  'Soldier',
  'Lawful Neutral',
  1800,
  JSON_OBJECT('str', 16, 'dex', 10, 'con', 16, 'int', 8, 'wis', 12, 'cha', 10),
  JSON_ARRAY('Shield', 'Armor'),
  JSON_ARRAY('Battleaxe', 'Rations'),
  'A veteran of many battles.',
  JSON_OBJECT('name', 'Fighter', 'hit_die', 10),
  JSON_OBJECT('name', 'Dwarf', 'constitution', 2)
),
(
  2,
  'Nyx',
  4, -- Tiefling
  3, -- Rogue
  2,
  'Criminal',
  'Chaotic Neutral',
  500,
  JSON_OBJECT('str', 9, 'dex', 16, 'con', 12, 'int', 13, 'wis', 10, 'cha', 14),
  JSON_ARRAY('Lockpicks', 'Cloak'),
  JSON_ARRAY('Dagger', 'Poison'),
  'A shadow in the night.',
  JSON_OBJECT('name', 'Rogue', 'hit_die', 8),
  JSON_OBJECT('name', 'Tiefling', 'charisma', 2)
);

-- =========================
-- CHARACTER EXPORTS
-- =========================
INSERT INTO character_exports (character_id, file_path, type, snapshot) VALUES
(
  1,
  '/exports/arannis.pdf',
  'pdf',
  JSON_OBJECT('name', 'Arannis', 'level', 3)
),
(
  2,
  '/exports/thorin.json',
  'json',
  JSON_OBJECT('name', 'Thorin', 'level', 4)
);