--
-- PostgreSQL specific database definitions
--

CREATE INDEX "idx_mstex_sid_dom_cont" ON "mshop_text" ("siteid", "domain", "content");
