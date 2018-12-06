--
-- PostgreSQL specific database definitions
--

CREATE INDEX "idx_msindte_content" ON "mshop_index_text" USING GIN (to_tsvector('english', "content"));
