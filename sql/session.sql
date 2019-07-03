CREATE TABLE IF NOT EXISTS anthrokit_sessions (
    sessionid TEXT PRIMARY KEY,
    data TEXT,
    created TIMESTAMP DEFAULT NOW(),
    updated TIMESTAMP
);
