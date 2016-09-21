/**
 * Database creation script
 */
/* Foreign key constrainst need to be explicitly enabled in SQLite */
PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS user;

CREATE TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    username VARCHAR NOT NULL,
    email VARCHAR NOT NULL UNIQUE,
    password VARCHAR NOT NULL,
    created_at VARCHAR NOT NULL,
    is_enabled BOOLEAN NOT NULL DEFAULT true
);

/*
This will become user = 1. Its created just to satisfy constraints here.
The password will be properly hashed in the installer
 */
INSERT INTO user (
    username, password, email, created_at, is_enabled
) VALUES (
    "admin", "unhashed-password", "admin@admin.com", datetime('now', '-3 months'), 0
);

DROP TABLE IF EXISTS post;

CREATE TABLE post (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    title VARCHAR NOT NULL,
    body VARCHAR NOT NULL,
    user_id INTEGER NOT NULL,
    created_at VARCHAR NOT NULL,
    updated_at VARCHAR,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

INSERT INTO post (
    title, body, user_id, created_at
) VALUES (
    "Here's our first post",
    "This is the body of the first post.
    It is split into paragraphs.",
    1,
    date('now', '-2 months')
);

INSERT INTO post (
    title, body, user_id, created_at
) VALUES(
    "Now for a second article",
    "This is the body of the second post.
    This is another paragraph.",
    1,
    date('now', '-40 days')
);

INSERT INTO post (
    title, body, user_id, created_at
) VALUES(
    "Here's a third post",
    "This is the body of the third post.
    This is split into paragraphs.",
    1,
    date('now', '-13 days')
);

DROP TABLE IF EXISTS comment;

CREATE TABLE comment (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    post_id INTEGER NOT NULL,
    created_at VARCHAR NOT NULL,
    name VARCHAR NOT NULL,
    website VARCHAR,
    text VARCHAR NOT NULL,
    FOREIGN KEY (post_id) REFERENCES post(id)
);

INSERT INTO comment (
    post_id, created_at, name, website, text
) VALUES(
    1,
    date('now', '-10 days'),
    'Jimmy',
    'http://example.com/',
    "This is Jimmy's contribution"
);

INSERT INTO comment (
    post_id, created_at, name, website, text
) VALUES(
    1,
    date('now', '-8 days'),
    'Jonny',
    'http://anotherexample.com/',
    "This is a comment from Jonny"
);
