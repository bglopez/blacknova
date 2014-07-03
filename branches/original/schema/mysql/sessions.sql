CREATE TABLE IF NOT EXISTS bnt_sessions (
  sesskey varchar(104) COLLATE utf8mb4_unicode_ci NOT NULL,
  expiry datetime NOT NULL,
  sessdata text COLLATE utf8mb4_unicode_ci
  PRIMARY KEY (sesskey),
  KEY bnt_sess2_expiry (expiry),
  KEY bnt_sess2_expireref (expireref)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
