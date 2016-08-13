--
-- PostgreSQL specific database definitions
--

CREATE INDEX "idx_msindte_p_s_lt_la_ty_do_va" ON "mshop_index_text" ("prodid", "siteid", "listtype", "langid", "type", "domain", "value");

CREATE INDEX "idx_msindte_value" ON "mshop_index_text" USING GIN (to_tsvector('english', "value"));
