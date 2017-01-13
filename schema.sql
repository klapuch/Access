--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.4
-- Dumped by pg_dump version 9.5.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: forgotten_passwords; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE forgotten_passwords (
    id integer NOT NULL,
    user_id integer NOT NULL,
    reminder character varying(141) NOT NULL,
    used boolean NOT NULL,
    reminded_at timestamp without time zone NOT NULL
);


ALTER TABLE forgotten_passwords OWNER TO postgres;

--
-- Name: forgotten_passwords_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE forgotten_passwords_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE forgotten_passwords_id_seq OWNER TO postgres;

--
-- Name: forgotten_passwords_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE forgotten_passwords_id_seq OWNED BY forgotten_passwords.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE users (
    id integer NOT NULL,
    email text NOT NULL,
    password character varying(255) NOT NULL
);


ALTER TABLE users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: verification_codes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE verification_codes (
    id integer NOT NULL,
    user_id integer NOT NULL,
    code character varying(91) NOT NULL,
    used boolean NOT NULL,
    used_at timestamp without time zone
);


ALTER TABLE verification_codes OWNER TO postgres;

--
-- Name: verification_codes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE verification_codes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE verification_codes_id_seq OWNER TO postgres;

--
-- Name: verification_codes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE verification_codes_id_seq OWNED BY verification_codes.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY forgotten_passwords ALTER COLUMN id SET DEFAULT nextval('forgotten_passwords_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY verification_codes ALTER COLUMN id SET DEFAULT nextval('verification_codes_id_seq'::regclass);


--
-- Name: forgotten_passwords_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY forgotten_passwords
    ADD CONSTRAINT forgotten_passwords_id PRIMARY KEY (id);


--
-- Name: users_email; Type: CONSTRAINT; Schema: public; Owner: postgres
--
CREATE UNIQUE INDEX users_email_unique_idx on users (LOWER(email)); 


--
-- Name: users_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_id PRIMARY KEY (id);


--
-- Name: verification_codes_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY verification_codes
    ADD CONSTRAINT verification_codes_id PRIMARY KEY (id);


--
-- Name: verification_codes_user_id; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY verification_codes
    ADD CONSTRAINT verification_codes_user_id UNIQUE (user_id);


--
-- Name: forgotten_passwords_user_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX forgotten_passwords_user_id ON forgotten_passwords USING btree (user_id);


--
-- Name: forgotten_passwords_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY forgotten_passwords
    ADD CONSTRAINT forgotten_passwords_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: verification_codes_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY verification_codes
    ADD CONSTRAINT verification_codes_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

