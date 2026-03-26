CREATE TABLE notes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    priority TEXT NOT NULL DEFAULT 'reference',
    is_pinned INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL,
    stored_path TEXT NOT NULL,
    original_name TEXT NOT NULL,
    mime_type TEXT,
    priority TEXT NOT NULL DEFAULT 'reference',
    is_pinned INTEGER NOT NULL DEFAULT 0,
    uploaded_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    created_at TEXT NOT NULL
);

CREATE TABLE item_tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_type TEXT NOT NULL,
    item_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL
);

CREATE TABLE links (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    from_type TEXT NOT NULL,
    from_id INTEGER NOT NULL,
    to_type TEXT NOT NULL,
    to_id INTEGER NOT NULL,
    link_type TEXT NOT NULL DEFAULT 'related',
    created_at TEXT NOT NULL
);

CREATE INDEX idx_notes_priority ON notes(priority);
CREATE INDEX idx_files_priority ON files(priority);
CREATE INDEX idx_tags_name ON tags(name);
CREATE INDEX idx_item_tags_item ON item_tags(item_type, item_id);
CREATE INDEX idx_item_tags_tag ON item_tags(tag_id);
CREATE INDEX idx_links_from ON links(from_type, from_id);
CREATE INDEX idx_links_to ON links(to_type, to_id);
