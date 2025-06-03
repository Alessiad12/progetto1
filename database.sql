--
-- PostgreSQL database dump
--

-- Dumped from database version 17.0
-- Dumped by pg_dump version 17.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: chat_viaggio; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.chat_viaggio (
    id integer NOT NULL,
    viaggio_id integer NOT NULL,
    utente_id integer NOT NULL,
    messaggio text NOT NULL,
    data_creazione timestamp without time zone DEFAULT now()
);


ALTER TABLE public.chat_viaggio OWNER TO postgres;

--
-- Name: chat_viaggio_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.chat_viaggio_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.chat_viaggio_id_seq OWNER TO postgres;

--
-- Name: chat_viaggio_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.chat_viaggio_id_seq OWNED BY public.chat_viaggio.id;


--
-- Name: esperienze_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.esperienze_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.esperienze_id_seq OWNER TO postgres;

--
-- Name: itinerari; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.itinerari (
    id integer NOT NULL,
    nome_itinerario character varying(255) NOT NULL,
    luoghi json NOT NULL,
    data_creazione timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    viaggio_id integer,
    utente_id integer
);


ALTER TABLE public.itinerari OWNER TO postgres;

--
-- Name: itinerari_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.itinerari_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.itinerari_id_seq OWNER TO postgres;

--
-- Name: itinerari_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.itinerari_id_seq OWNED BY public.itinerari.id;


--
-- Name: notifiche; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.notifiche (
    utente_id integer NOT NULL,
    mittente_id integer NOT NULL,
    viaggio_id integer NOT NULL,
    titolo_viaggio character varying(255),
    letta boolean DEFAULT false,
    data_creazione timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    tipo text,
    id integer NOT NULL
);


ALTER TABLE public.notifiche OWNER TO postgres;

--
-- Name: notifiche_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.notifiche_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.notifiche_id_seq OWNER TO postgres;

--
-- Name: notifiche_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.notifiche_id_seq OWNED BY public.notifiche.id;


--
-- Name: preferenze_utente_viaggio; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.preferenze_utente_viaggio (
    utente_id integer,
    email text,
    destinazione character varying(100),
    data_partenza date,
    data_ritorno date,
    budget character varying(20),
    tipo_viaggio character varying(50),
    compagnia character varying(50)
);


ALTER TABLE public.preferenze_utente_viaggio OWNER TO postgres;

--
-- Name: profili; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.profili (
    id integer NOT NULL,
    email text NOT NULL,
    nome character varying(100),
    eta integer,
    bio text,
    colore_sfondo character varying(10) DEFAULT '#faf3bfc4'::character varying,
    data_di_nascita date,
    immagine_profilo text,
    posizione_immagine text
);


ALTER TABLE public.profili OWNER TO postgres;

--
-- Name: swipes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.swipes (
    user_id integer NOT NULL,
    trip_id integer NOT NULL,
    is_like boolean NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.swipes OWNER TO postgres;

--
-- Name: utenti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.utenti (
    id integer NOT NULL,
    nome text NOT NULL,
    nickname text NOT NULL,
    email text NOT NULL,
    data_di_nascita date,
    password text NOT NULL
);


ALTER TABLE public.utenti OWNER TO postgres;

--
-- Name: utenti_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.utenti_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.utenti_id_seq OWNER TO postgres;

--
-- Name: utenti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.utenti_id_seq OWNED BY public.utenti.id;


--
-- Name: viaggi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.viaggi (
    id integer NOT NULL,
    user_id integer,
    destinazione character varying(100),
    data_partenza date,
    data_ritorno date,
    budget character varying(20),
    tipo_viaggio character varying(50),
    lingua character varying(50),
    compagnia character varying(50),
    descrizione text,
    foto text,
    latitudine numeric(10,8),
    longitudine numeric(11,8)
);


ALTER TABLE public.viaggi OWNER TO postgres;

--
-- Name: viaggi_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.viaggi_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.viaggi_id_seq OWNER TO postgres;

--
-- Name: viaggi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.viaggi_id_seq OWNED BY public.viaggi.id;


--
-- Name: viaggi_terminati; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.viaggi_terminati (
    id integer NOT NULL,
    utente_id integer NOT NULL,
    viaggio_id integer NOT NULL,
    descrizione text NOT NULL,
    valutazione integer NOT NULL,
    foto1 text,
    foto2 text,
    foto3 text,
    foto4 text,
    foto5 text,
    data_creazione timestamp without time zone DEFAULT now() NOT NULL,
    natura integer DEFAULT 0,
    relax integer DEFAULT 0,
    monumenti integer DEFAULT 0,
    cultura integer DEFAULT 0,
    nightlife integer DEFAULT 0
);


ALTER TABLE public.viaggi_terminati OWNER TO postgres;

--
-- Name: viaggi_terminati_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.viaggi_terminati_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.viaggi_terminati_id_seq OWNER TO postgres;

--
-- Name: viaggi_terminati_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.viaggi_terminati_id_seq OWNED BY public.viaggi_terminati.id;


--
-- Name: viaggi_utenti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.viaggi_utenti (
    viaggio_id integer NOT NULL,
    user_id integer NOT NULL,
    ruolo character varying(20) DEFAULT 'partecipante'::character varying
);


ALTER TABLE public.viaggi_utenti OWNER TO postgres;

--
-- Name: chat_viaggio id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chat_viaggio ALTER COLUMN id SET DEFAULT nextval('public.chat_viaggio_id_seq'::regclass);


--
-- Name: itinerari id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.itinerari ALTER COLUMN id SET DEFAULT nextval('public.itinerari_id_seq'::regclass);


--
-- Name: notifiche id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notifiche ALTER COLUMN id SET DEFAULT nextval('public.notifiche_id_seq'::regclass);


--
-- Name: utenti id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utenti ALTER COLUMN id SET DEFAULT nextval('public.utenti_id_seq'::regclass);


--
-- Name: viaggi id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viaggi ALTER COLUMN id SET DEFAULT nextval('public.viaggi_id_seq'::regclass);


--
-- Name: viaggi_terminati id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viaggi_terminati ALTER COLUMN id SET DEFAULT nextval('public.viaggi_terminati_id_seq'::regclass);


--
-- Data for Name: chat_viaggio; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.chat_viaggio (id, viaggio_id, utente_id, messaggio, data_creazione) FROM stdin;
1	1	7	fghjk	2025-05-07 10:23:51.619263
2	1	7	ciao	2025-05-07 10:24:03.421753
3	4	4	ida fanculo	2025-05-07 18:25:16.030412
4	4	9	vero	2025-05-07 18:41:15.287931
5	4	4	iiii	2025-05-08 15:52:01.318565
6	4	4	njn	2025-05-08 15:52:01.563882
7	4	4	ciao	2025-05-08 15:52:34.818529
8	4	9	ciao	2025-05-08 15:53:15.196427
9	12	8	ciao!pronto a partire?	2025-05-09 18:34:04.355261
10	12	4	prontissimo! andiamo!	2025-05-09 18:36:13.914239
11	13	8	per quale roma vuoi partire	2025-05-09 19:08:35.612891
12	13	4	dove vuoi	2025-05-09 19:10:44.573899
13	13	8	ciao	2025-05-12 12:24:54.610276
14	15	8	ciao	2025-05-14 18:55:21.637901
15	18	4	ciao	2025-05-14 19:05:55.443452
16	9	7	ciao	2025-05-22 08:35:45.825377
17	26	9	ciao	2025-05-22 09:00:15.835441
18	26	6	ciao a te	2025-05-22 09:00:21.1025
19	39	14	ciaooo	2025-05-22 11:03:53.093162
20	39	7	dc	2025-05-22 11:04:19.677361
21	9	7	gkighk	2025-05-22 14:42:30.942564
22	9	8	kugoi	2025-05-22 14:43:38.131273
23	9	7	ciao	2025-05-22 17:57:31.315501
24	9	7	ciaopksèpa	2025-05-22 17:58:20.863259
25	9	8	belooo!	2025-05-22 17:58:30.667571
26	19	7	ciao francesco	2025-05-22 20:14:59.380319
27	19	6	ciao ida	2025-05-22 20:15:06.029113
28	28	7	ciao alessia	2025-05-22 20:52:41.334769
29	28	8	ciao ida	2025-05-22 20:52:48.758559
30	48	8	ciao Ida, come stai?	2025-05-22 21:52:44.374053
31	48	7	tutto bene, da dove parti?	2025-05-22 21:53:03.837668
32	42	2	ciao	2025-05-27 09:02:21.059936
33	42	4	ciao Luca	2025-05-27 09:02:30.494733
\.


--
-- Data for Name: itinerari; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.itinerari (id, nome_itinerario, luoghi, data_creazione, viaggio_id, utente_id) FROM stdin;
1	Roma	["colosseo"]	2025-05-22 09:31:37.557443	\N	\N
2	wshn	["colosseo"]	2025-05-22 10:00:55.337274	4	7
3	Budapest	["rudas","parlamento","gellert","gellert, budapest","Danubio, budapest","sasad"]	2025-05-22 10:19:26.597526	39	7
4	budapest	["parlamento"]	2025-05-22 10:39:37.594261	39	7
5	oih	["parlamento"]	2025-05-22 11:05:58.301614	39	7
6	Portogallo	["porto","CENTRO STORICO, porto","parlamento, porto","porto, porto, Portogallo","quinta marques Gomes"]	2025-05-22 15:25:10.563176	16	7
7	New York con Aldo	["soho","soho, New York","Manhattan","upper east side","Central Park","moma"]	2025-05-22 15:27:51.710539	10	9
8	Giappone 2025	["Sakura","Osaka","tokyo"]	2025-05-22 15:56:56.187258	15	7
9	Roma	["Marco Polo, san lorenzo","santa maria maggiore","Colosseo","fori imperiali","porta pia","basilica san Pietro","villa borghese"]	2025-05-26 17:35:10.86851	12	4
10	roma 2	["colosseo"]	2025-05-26 17:38:33.397256	12	4
11	Tokyo	[]	2025-05-26 17:42:36.329228	18	4
\.


--
-- Data for Name: notifiche; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.notifiche (utente_id, mittente_id, viaggio_id, titolo_viaggio, letta, data_creazione, tipo, id) FROM stdin;
1	9	1	Giappone	f	2025-05-07 18:22:20.084211	like	132
2	9	2	Spagna	f	2025-05-07 18:22:21.530452	like	133
3	9	3	Francia	f	2025-05-07 18:22:22.480367	like	134
4	9	4	Thailandia	f	2025-05-07 18:22:27.180749	like	135
5	9	5	Australia	f	2025-05-07 18:22:29.814438	like	137
6	9	6	USA	f	2025-05-07 18:22:31.200953	like	139
7	9	7	Canada	f	2025-05-07 18:22:32.461536	like	141
8	9	8	Portogallo	f	2025-05-07 18:22:33.778138	like	143
7	9	9	roma	f	2025-05-07 18:22:34.977981	like	145
9	9	10	New York	f	2025-05-07 18:22:36.17635	like	147
1	4	1	Giappone	f	2025-05-07 18:23:27.480237	like	151
2	4	2	Spagna	f	2025-05-07 18:23:28.642372	like	153
3	4	3	Francia	f	2025-05-07 18:23:54.244007	like	158
4	4	4	Thailandia	f	2025-05-07 18:23:55.289357	like	159
5	4	5	Australia	f	2025-05-07 18:23:56.059469	like	160
6	4	6	USA	f	2025-05-07 18:23:56.840479	like	162
7	4	7	Canada	f	2025-05-07 18:23:58.086202	like	164
8	4	8	Portogallo	f	2025-05-07 18:23:58.959136	like	165
7	4	9	roma	f	2025-05-07 18:23:59.845157	like	166
9	4	10	New York	f	2025-05-07 18:24:06.108132	like	167
4	4	4	Thailandia	f	2025-05-07 18:24:30.493777	match_accepted	168
1	8	1	Giappone	f	2025-05-07 18:45:15.842825	like	204
2	8	2	Spagna	f	2025-05-07 18:45:16.799747	like	205
3	8	3	Francia	f	2025-05-07 18:45:17.538807	like	206
4	8	4	Thailandia	f	2025-05-07 18:45:23.413518	like	207
5	8	5	Australia	f	2025-05-07 18:45:24.15683	like	208
6	8	6	USA	f	2025-05-07 18:45:24.876548	like	209
7	8	7	Canada	f	2025-05-07 18:45:26.223731	like	211
8	8	8	Portogallo	f	2025-05-07 18:45:27.333088	like	213
7	8	9	roma	f	2025-05-07 18:45:28.423853	like	214
9	8	10	New York	f	2025-05-07 18:45:29.374341	like	215
8	8	8	Portogallo	f	2025-05-07 18:45:47.327914	match_accepted	216
8	8	8	Portogallo	f	2025-05-07 18:46:00.792582	registra_viaggio	218
1	7	1	Giappone	f	2025-05-09 18:18:38.677791	like	313
2	7	2	Spagna	f	2025-05-09 18:18:39.647039	like	315
3	7	3	Francia	f	2025-05-09 18:18:40.629939	like	316
4	7	4	Thailandia	f	2025-05-09 18:18:41.588519	like	317
6	8	26	Barcellona	f	2025-05-20 10:47:20.961291	like	371
9	9	10	New York	f	2025-05-21 21:39:01.054446	registra_viaggio	372
4	4	4	Thailandia	f	2025-05-21 21:39:01.069579	registra_viaggio	373
9	4	4	Thailandia	f	2025-05-21 21:39:01.075632	registra_viaggio	374
8	4	4	Thailandia	f	2025-05-21 21:39:01.079457	registra_viaggio	375
7	4	4	Thailandia	f	2025-05-21 21:39:01.081822	registra_viaggio	376
9	1	1	Giappone	f	2025-05-21 21:39:01.085316	registra_viaggio	377
7	7	15	Giappone	f	2025-05-21 21:39:01.087527	registra_viaggio	378
8	7	15	Giappone	f	2025-05-21 21:39:01.09219	registra_viaggio	379
8	8	16	Portogallo	f	2025-05-21 21:39:01.096871	registra_viaggio	380
7	8	16	Portogallo	f	2025-05-21 21:39:01.099781	registra_viaggio	381
8	8	17	Giappone	f	2025-05-21 21:39:01.106104	registra_viaggio	382
4	4	18	Tokyo	f	2025-05-21 21:39:01.112705	registra_viaggio	383
13	4	18	Tokyo	f	2025-05-21 21:39:01.115801	registra_viaggio	384
6	6	26	Barcellona	f	2025-05-21 21:39:01.119425	registra_viaggio	385
6	9	26	Barcellona	f	2025-05-21 21:59:31.325053	like	386
9	8	10	New York	f	2025-05-21 21:59:54.860128	match_accepted	387
8	9	10	New York	f	2025-05-21 21:59:54.941596	match_accepted	388
8	9	10	New York	f	2025-05-21 22:00:00.093783	registra_viaggio	389
6	9	26	Barcellona	f	2025-05-21 22:00:40.49189	like	390
6	9	26	Barcellona	f	2025-05-21 22:08:21.002445	like	391
6	9	26	Barcellona	f	2025-05-21 22:09:11.202872	like	392
6	9	26	Barcellona	f	2025-05-21 22:11:08.912317	like	393
6	9	26	Barcellona	f	2025-05-21 22:12:22.990695	like	394
6	9	26	Barcellona	f	2025-05-21 22:12:29.021316	like	395
6	9	26	Barcellona	f	2025-05-21 22:15:30.792306	like	396
6	9	26	Barcellona	f	2025-05-21 22:18:20.214883	like	397
6	9	26	Barcellona	f	2025-05-21 22:18:27.71159	like	398
6	9	26	Barcellona	f	2025-05-21 22:18:41.343521	like	399
6	9	26	Barcellona	f	2025-05-21 22:22:28.922891	like	400
6	9	26	Barcellona	f	2025-05-21 22:22:42.145489	like	401
6	9	26	Barcellona	f	2025-05-21 22:23:41.259066	like	402
6	9	26	Barcellona	f	2025-05-21 22:23:51.717813	like	403
6	9	26	Barcellona	f	2025-05-21 22:24:22.128633	like	404
6	9	26	Barcellona	f	2025-05-21 22:24:27.371871	like	405
6	9	26	Barcellona	f	2025-05-21 22:24:31.251874	like	406
6	9	26	Barcellona	f	2025-05-21 22:24:55.102511	like	407
6	9	26	Barcellona	f	2025-05-21 22:26:03.058118	like	408
6	9	26	Barcellona	f	2025-05-21 22:26:10.193978	like	409
6	9	26	Barcellona	f	2025-05-21 22:26:28.342718	like	410
6	9	26	Barcellona	f	2025-05-21 22:26:28.429309	like	411
6	9	26	Barcellona	f	2025-05-21 22:26:28.51289	like	412
6	9	26	Barcellona	f	2025-05-21 22:26:28.599197	like	413
6	9	26	Barcellona	f	2025-05-21 22:26:35.679269	like	414
6	9	26	Barcellona	f	2025-05-21 22:26:35.778279	like	415
6	9	26	Barcellona	f	2025-05-21 22:26:35.87958	like	416
6	9	26	Barcellona	f	2025-05-21 22:26:35.963414	like	417
7	7	39	Budapest	f	2025-05-22 08:35:00.902119	registra_viaggio	418
5	5	43	Lisbona	f	2025-05-22 08:35:00.907209	registra_viaggio	419
7	8	9	roma	f	2025-05-22 08:35:38.017242	match_accepted	420
8	7	9	roma	f	2025-05-22 08:35:38.041704	match_accepted	421
8	7	9	roma	f	2025-05-22 08:36:00.969025	registra_viaggio	422
6	9	26	Barcellona	f	2025-05-22 08:59:02.859159	like	423
6	9	26	Barcellona	f	2025-05-22 08:59:11.132714	like	424
6	9	26	Barcellona	f	2025-05-22 08:59:11.159716	like	425
6	9	26	Barcellona	f	2025-05-22 08:59:11.183581	like	426
6	9	26	Barcellona	f	2025-05-22 08:59:11.209221	like	427
6	9	26	Barcellona	f	2025-05-22 08:59:14.497563	like	428
6	9	26	Barcellona	f	2025-05-22 09:00:01.695952	match_accepted	429
9	6	26	Barcellona	f	2025-05-22 09:00:01.70971	match_accepted	430
9	6	26	Barcellona	f	2025-05-22 09:01:00.796518	registra_viaggio	431
2	7	29	Parigi	f	2025-05-22 10:37:10.143077	like	432
2	7	29	Parigi	f	2025-05-22 10:37:10.167226	like	433
2	7	29	Parigi	f	2025-05-22 10:37:10.181989	like	434
2	7	29	Parigi	f	2025-05-22 10:37:10.200418	like	435
6	7	19	Berlino	f	2025-05-22 10:37:11.992729	like	436
2	7	29	Parigi	f	2025-05-22 10:37:18.850394	like	437
7	14	53	Bruxelles	f	2025-05-22 11:02:15.693762	like	438
7	14	53	Bruxelles	f	2025-05-22 11:02:15.730963	like	439
7	14	53	Bruxelles	f	2025-05-22 11:02:15.751001	like	440
7	14	53	Bruxelles	f	2025-05-22 11:02:15.770707	like	441
7	14	39	Budapest	f	2025-05-22 11:02:29.429522	like	442
7	14	39	Budapest	f	2025-05-22 11:02:29.456357	like	443
7	14	39	Budapest	f	2025-05-22 11:02:29.481038	like	444
7	14	39	Budapest	f	2025-05-22 11:02:29.503125	like	445
7	14	53	Bruxelles	f	2025-05-22 11:02:32.753526	like	446
7	14	53	Bruxelles	f	2025-05-22 11:02:32.778203	like	447
7	14	53	Bruxelles	f	2025-05-22 11:02:32.807827	like	448
7	14	53	Bruxelles	f	2025-05-22 11:02:32.827855	like	449
7	14	39	Budapest	f	2025-05-22 11:03:36.530085	match_accepted	450
14	7	39	Budapest	f	2025-05-22 11:03:36.553467	match_accepted	451
14	7	39	Budapest	f	2025-05-22 11:04:00.525271	registra_viaggio	452
2	7	29	Parigi	f	2025-05-22 14:40:38.105855	like	453
2	7	29	Parigi	f	2025-05-22 14:40:41.29697	like	454
6	7	19	Berlino	f	2025-05-22 14:40:45.860697	like	455
6	7	19	Berlino	f	2025-05-22 14:40:45.888087	like	456
6	7	19	Berlino	f	2025-05-22 14:40:45.911016	like	457
6	7	19	Berlino	f	2025-05-22 14:40:45.930693	like	458
7	8	9	roma	f	2025-05-22 14:42:13.626441	match_accepted	459
2	7	29	Parigi	f	2025-05-22 16:00:04.819898	like	460
2	7	29	Parigi	f	2025-05-22 16:00:11.711078	like	461
6	7	19	Berlino	f	2025-05-22 16:00:13.310843	like	462
2	7	29	Parigi	f	2025-05-22 20:12:55.805267	like	463
2	7	29	Parigi	f	2025-05-22 20:12:55.834429	like	464
2	7	29	Parigi	f	2025-05-22 20:12:55.852173	like	465
2	7	29	Parigi	f	2025-05-22 20:12:55.961227	like	466
6	7	19	Berlino	f	2025-05-22 20:12:58.077408	like	467
2	7	29	Parigi	f	2025-05-22 20:13:01.590624	like	468
2	7	29	Parigi	f	2025-05-22 20:14:00.612052	like	469
6	7	19	Berlino	f	2025-05-22 20:14:11.220665	like	470
6	7	19	Berlino	f	2025-05-22 20:14:37.816579	match_accepted	471
7	6	19	Berlino	f	2025-05-22 20:14:37.846238	match_accepted	472
2	7	42	Bologna	f	2025-05-22 20:47:53.333196	like	473
8	7	48	Porto	f	2025-05-22 20:47:57.405261	like	474
8	7	48	Porto	f	2025-05-22 20:47:57.439761	like	475
8	7	48	Porto	f	2025-05-22 20:47:57.463948	like	476
8	7	48	Porto	f	2025-05-22 20:47:57.485139	like	477
8	7	28	Amsterdam	f	2025-05-22 20:48:04.050296	like	478
8	7	28	Amsterdam	f	2025-05-22 20:52:01.349302	like	479
8	7	28	Amsterdam	f	2025-05-22 20:52:27.303801	match_accepted	480
7	8	28	Amsterdam	f	2025-05-22 20:52:27.327754	match_accepted	481
8	7	48	Porto	f	2025-05-22 21:51:06.169054	like	482
8	7	48	Porto	f	2025-05-22 21:51:29.209663	like	483
8	7	48	Porto	f	2025-05-22 21:52:22.080983	match_accepted	484
7	8	48	Porto	f	2025-05-22 21:52:22.107002	match_accepted	485
7	7	33	Bruxelles	f	2025-05-23 00:00:00.950155	registra_viaggio	486
3	7	30	Londra	f	2025-05-24 01:52:36.135035	like	487
8	8	12	Roma	f	2025-05-26 15:41:00.874606	registra_viaggio	488
4	8	12	Roma	f	2025-05-26 15:41:00.878968	registra_viaggio	489
8	8	13	Roma	f	2025-05-26 15:41:00.880466	registra_viaggio	490
4	8	13	Roma	f	2025-05-26 15:41:00.881605	registra_viaggio	491
3	7	30	Londra	f	2025-05-26 16:04:32.979414	like	492
3	7	30	Londra	f	2025-05-26 16:07:00.246677	match_accepted	493
7	3	30	Londra	f	2025-05-26 16:07:00.281377	match_accepted	494
3	8	3	Francia	f	2025-05-26 16:09:05.917946	match_accepted	495
8	3	3	Francia	f	2025-05-26 16:09:05.927271	match_accepted	496
8	3	3	Francia	f	2025-05-26 16:10:00.175832	registra_viaggio	497
3	9	3	Francia	f	2025-05-26 16:17:17.670358	match_accepted	498
9	3	3	Francia	f	2025-05-26 16:17:17.697078	match_accepted	499
9	3	3	Francia	f	2025-05-26 16:18:00.78527	registra_viaggio	500
4	7	4	Thailandia	f	2025-05-26 16:19:36.976149	match_accepted	501
1	9	1	Giappone	f	2025-05-26 16:20:32.642016	match_accepted	502
1	8	1	Giappone	f	2025-05-26 16:20:59.249501	match_accepted	503
8	1	1	Giappone	f	2025-05-26 16:20:59.287042	match_accepted	504
8	1	1	Giappone	f	2025-05-26 16:21:00.986101	registra_viaggio	505
1	8	1	Giappone	f	2025-05-26 16:23:19.000579	match_accepted	506
3	7	49	Copenaghen	f	2025-05-26 16:23:47.284156	like	507
1	7	1	Giappone	f	2025-05-26 16:23:50.376338	match_accepted	508
7	1	1	Giappone	f	2025-05-26 16:23:50.385812	match_accepted	509
7	1	1	Giappone	f	2025-05-26 16:24:00.226329	registra_viaggio	510
6	7	19	Berlino	f	2025-05-26 16:38:21.541533	match_accepted	511
6	9	26	Barcellona	f	2025-05-26 16:38:27.991506	match_accepted	512
3	7	49	Copenaghen	f	2025-05-26 16:38:38.747062	like	513
3	7	49	Copenaghen	f	2025-05-26 16:38:57.890379	match_accepted	514
7	3	49	Copenaghen	f	2025-05-26 16:38:57.91704	match_accepted	515
7	14	53	Bruxelles	f	2025-05-26 16:49:01.541344	match_accepted	516
14	7	53	Bruxelles	f	2025-05-26 16:49:01.566586	match_accepted	517
7	14	53	Bruxelles	f	2025-05-26 16:49:15.880544	match_accepted	518
6	7	19	Berlino	f	2025-05-26 16:50:02.901003	match_accepted	519
6	8	26	Barcellona	f	2025-05-26 16:56:40.353999	like	520
6	8	26	Barcellona	f	2025-05-26 16:56:55.309579	match_accepted	521
8	6	26	Barcellona	f	2025-05-26 16:56:55.336251	match_accepted	522
8	6	26	Barcellona	f	2025-05-26 16:57:00.867179	registra_viaggio	523
2	6	42	Bologna	f	2025-05-26 16:59:08.376012	like	524
2	6	42	Bologna	f	2025-05-26 16:59:30.761782	match_accepted	525
6	2	42	Bologna	f	2025-05-26 16:59:30.787996	match_accepted	526
8	6	48	Porto	f	2025-05-26 17:09:44.08341	like	527
8	6	48	Porto	f	2025-05-26 17:09:58.849998	match_accepted	528
6	8	48	Porto	f	2025-05-26 17:09:58.86934	match_accepted	529
7	6	54	Perugia	f	2025-05-26 17:10:27.837371	like	530
7	6	54	Perugia	f	2025-05-26 17:10:53.570504	match_accepted	531
6	7	54	Perugia	f	2025-05-26 17:10:53.591528	match_accepted	532
7	6	55	Oslo	f	2025-05-26 17:16:45.024735	like	533
7	6	55	Oslo	f	2025-05-26 17:17:04.473173	match_accepted	534
6	7	55	Oslo	f	2025-05-26 17:17:04.489692	match_accepted	535
7	6	54	Perugia	f	2025-05-26 17:17:11.262704	match_accepted	536
8	6	28	Amsterdam	f	2025-05-26 17:19:42.720907	like	537
8	6	28	Amsterdam	f	2025-05-26 17:19:50.388023	match_accepted	538
6	8	28	Amsterdam	f	2025-05-26 17:19:50.397589	match_accepted	539
8	6	56	Varsavia	f	2025-05-26 17:24:36.65844	like	540
8	6	56	Varsavia	f	2025-05-26 17:24:45.836019	match_accepted	541
6	8	56	Varsavia	f	2025-05-26 17:24:45.846731	match_accepted	542
2	4	42	Bologna	f	2025-05-26 17:29:04.662141	like	543
7	4	55	Oslo	f	2025-05-26 17:29:40.270802	like	544
8	4	56	Varsavia	f	2025-05-26 17:29:56.545781	like	545
8	4	48	Porto	f	2025-05-26 17:30:14.704067	like	546
7	4	54	Perugia	f	2025-05-26 17:30:15.928441	like	547
8	4	28	Amsterdam	f	2025-05-26 17:30:17.011916	like	548
8	4	28	Amsterdam	f	2025-05-26 17:30:46.733384	match_accepted	549
4	8	28	Amsterdam	f	2025-05-26 17:30:46.754343	match_accepted	550
8	4	28	Amsterdam	f	2025-05-26 19:12:29.430424	match_accepted	551
2	4	42	Bologna	f	2025-05-27 08:58:56.474039	like	552
2	4	42	Bologna	f	2025-05-27 08:59:10.533588	match_accepted	553
4	2	42	Bologna	f	2025-05-27 08:59:10.547201	match_accepted	554
8	8	28	Amsterdam	f	2025-06-03 22:13:00.806773	registra_viaggio	555
7	8	28	Amsterdam	f	2025-06-03 22:13:00.811491	registra_viaggio	556
6	8	28	Amsterdam	f	2025-06-03 22:13:00.812025	registra_viaggio	557
4	8	28	Amsterdam	f	2025-06-03 22:13:00.812501	registra_viaggio	558
7	7	54	Perugia	f	2025-06-03 22:13:00.814323	registra_viaggio	559
6	7	54	Perugia	f	2025-06-03 22:13:00.815026	registra_viaggio	560
2	15	29	Parigi	f	2025-06-03 22:23:36.702902	like	561
2	15	29	Parigi	f	2025-06-03 22:23:37.175124	like	562
6	15	19	Berlino	f	2025-06-03 22:23:37.552024	like	563
\.


--
-- Data for Name: preferenze_utente_viaggio; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.preferenze_utente_viaggio (utente_id, email, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio, compagnia) FROM stdin;
10	ale@ida.it	\N	\N	\N	\N	ristoranti	coppia
1	anna.bianchi@example.com	Parigi	2025-06-01	2025-06-10	500-1000€	natura	coppia
2	luca.verdi@example.com	New York	2025-07-15	2025-07-25	2000-3000€	spiaggia	gruppo
3	giulia.neri@example.com	Tokyo	2025-09-01	2025-09-15	3000-4000€	ristoranti	coppia
5	s.gallo@example.com	Londra	2025-08-10	2025-08-20	1000-2000€	musei	gruppo
11	m@a.it	Europa	2025-05-12	2025-02-20	1000-3000	natura	gruppo
13	mario@icloud.it	Asia	2025-05-07	2025-05-14	3500-5000	natura	gruppo
8	ale.desi@gmail.com	Europa	2025-04-20	2025-04-25	20-40000	spiaggia	gruppo
9	b@e.it	Europa	2025-04-20	2025-04-25	20-40000	spiaggia	gruppo
14	lorenzosdf@gmail.com	Europa	2025-05-01	2025-05-30	100-3000	musei	gruppo
10	ale@ida.it	\N	\N	\N	\N	ristoranti	coppia
1	anna.bianchi@example.com	Parigi	2025-06-01	2025-06-10	500-1000€	natura	coppia
2	luca.verdi@example.com	New York	2025-07-15	2025-07-25	2000-3000€	spiaggia	gruppo
3	giulia.neri@example.com	Tokyo	2025-09-01	2025-09-15	3000-4000€	ristoranti	coppia
5	s.gallo@example.com	Londra	2025-08-10	2025-08-20	1000-2000€	musei	gruppo
11	m@a.it	Europa	2025-05-12	2025-02-20	1000-3000	natura	gruppo
13	mario@icloud.it	Asia	2025-05-07	2025-05-14	3500-5000	natura	gruppo
8	ale.desi@gmail.com	Europa	2025-04-20	2025-04-25	20-40000	spiaggia	gruppo
9	b@e.it	Europa	2025-04-20	2025-04-25	20-40000	spiaggia	gruppo
14	lorenzosdf@gmail.com	Europa	2025-05-01	2025-05-30	100-3000	musei	gruppo
6	fra@gmail.com	Europa	2025-05-31	2025-06-02	10-12345678	ristoranti	gruppo
6	fra@gmail.com	Europa	2025-05-31	2025-06-02	10-12345678	ristoranti	gruppo
4	marco.rossi@example.com	Europa	2025-05-30	2025-06-08	10-30000	ristoranti	gruppo
4	marco.rossi@example.com	Europa	2025-05-30	2025-06-08	10-30000	ristoranti	gruppo
7	ida@ida.it	Europa	2025-05-29	2025-06-02	10-2000	ristoranti	gruppo
7	ida@ida.it	Europa	2025-05-29	2025-06-02	10-2000	ristoranti	gruppo
15	benvenutogiusy8@gmail.com	Europa	2025-06-03	2025-06-06	10-30000	musei	gruppo
\.


--
-- Data for Name: profili; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.profili (id, email, nome, eta, bio, colore_sfondo, data_di_nascita, immagine_profilo, posizione_immagine) FROM stdin;
1	anna.bianchi@example.com	Anna Bianchi	35	Mi piace viaggiare e scoprire nuovi luoghi. Appassionata di fotografia.	#faf3bfc4	1990-04-10	immagini/default.png	
2	luca.verdi@example.com	Luca Verdi	36	Adoro la musica e il buon cibo. Viaggio spesso per lavoro.	#faf3bfc4	1988-11-22	immagini/default.png	
3	giulia.neri@example.com	Giulia Neri	33	Sono un’appassionata di sport e natura. Mi piace fare trekking.	#faf3bfc4	1992-07-30	immagini/default.png	
5	s.gallo@xampre.com	Sara Gallo	29	Viaggiare è una delle cose che mi rende felice, ma amo anche il buon cinema.	#faf3bfc4	1995-12-01	immagini/default.png	
6	fra@gmail.com	Francesco Esposito	32	Tecnologia e viaggi, la mia vita in poche parole. Sempre in cerca di avventure.	#faf3bfc4	1993-03-20	immagini/default.png	
8	ale.desi@gmail.com	Alessia Desideri	22	Futura architetta e viaggiatrice nel cuore. Amo la cultura e l’arte.	#fbe0ce	2003-06-12	uploads/profilo_681b8e190dfe7.png	50
4	marco.rossi@xample.com	Marco Rossi	28	Viaggiare è la mia passione. Ho una collezione di mappe antiche.	#cee3f4	1997-05-15	uploads/profilo_681e37ada4a57.png	50
11	m@a.it	marcolino	21		#faf3bfc4	2003-10-12	\N	\N
12	mario@desi.it	mariodesi	57	mi piace viaggiare 	#faf3bfc4	1968-01-30	uploads/6824c58a6e8dc_Screenshot 2025-05-13 223922.png	\N
13	mario@icloud.it	mariodeside	57	Amo viaggiare e collezionare calamite.	#fbfbce	1968-01-30	uploads/profilo_6824c9416ceb0.png	50
10	ale@ida.it	Alida	21	Amo viaggiare e collezionare calamite.	#faf3bfc4	2003-08-12	uploads/profilo_682e2e794401c.jpg	50
9	b@e.it	betta	22	Mi piace viaggiare con il mio ragazzo.	#cee3f4	20003-04-17	uploads/profilo_682e309c56f84.png	50
7	ida@ida.it	Ida Benvenuto	22	Studentessa di design e amante della moda. Viaggiare mi ispira moltissimo.	#f4cedc	2003-08-19	uploads/profilo_681a409cd4722.jpg	50
14	lorenzosdf@gmail.com	bebbu	0		#fbfbce	2025-05-16	uploads/profilo_68347f453c468.png	50
15	benvenutogiusy8@gmail.com	b_giusy	24	Amo scoprire nuovi posti e culture	#faf3bfc4	2000-12-08	uploads/683f5993c2d2a_	\N
\.


--
-- Data for Name: swipes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.swipes (user_id, trip_id, is_like, created_at) FROM stdin;
8	1	f	2025-05-07 18:47:07.314149
8	2	t	2025-05-07 18:47:09.809595
8	3	t	2025-05-07 18:47:10.701762
8	4	t	2025-05-07 18:47:11.577593
8	5	t	2025-05-07 18:47:13.20366
8	6	t	2025-05-07 18:47:14.53045
8	7	t	2025-05-07 18:47:15.585536
8	8	t	2025-05-07 18:47:16.312463
8	9	t	2025-05-07 18:47:17.309934
4	1	t	2025-05-08 22:54:27.966222
8	10	t	2025-05-07 18:47:31.1144
4	2	t	2025-05-08 22:54:28.32456
4	3	t	2025-05-08 22:54:29.280009
4	4	t	2025-05-08 22:54:30.963383
4	5	f	2025-05-08 22:54:32.231156
4	6	f	2025-05-08 22:54:33.23584
4	7	t	2025-05-08 22:54:34.885104
4	8	t	2025-05-08 22:54:36.325892
4	10	f	2025-05-08 22:54:39.000614
9	1	t	2025-05-09 16:35:19.688678
9	2	t	2025-05-09 16:35:20.480061
9	3	t	2025-05-09 16:35:22.109908
9	4	f	2025-05-09 16:35:23.261539
9	5	t	2025-05-09 16:35:24.213299
9	6	t	2025-05-09 16:35:25.111706
9	7	f	2025-05-09 16:35:25.856752
9	8	f	2025-05-09 16:35:27.5026
9	9	f	2025-05-09 16:35:28.259817
9	10	t	2025-05-09 16:35:29.390174
7	4	t	2025-05-09 18:18:41.555065
7	5	f	2025-05-09 18:18:42.550315
7	6	f	2025-05-09 18:18:43.928058
7	7	t	2025-05-09 18:18:44.634501
7	1	t	2025-05-09 18:19:03.105614
7	2	t	2025-05-09 18:19:04.220926
7	3	t	2025-05-09 18:19:04.787319
4	9	t	2025-05-09 18:33:00.846387
4	12	t	2025-05-09 18:38:23.322361
4	13	t	2025-05-09 19:07:50.718314
8	14	t	2025-05-12 12:24:28.864126
7	16	t	2025-05-13 22:47:16.908166
8	15	t	2025-05-14 08:50:28.358474
13	18	t	2025-05-14 19:05:14.314282
9	26	t	2025-05-22 08:59:14.491332
14	39	t	2025-05-22 11:02:29.499438
14	53	t	2025-05-22 11:02:32.824557
7	19	t	2025-05-22 20:14:11.203015
7	28	t	2025-05-22 20:52:01.338592
7	48	t	2025-05-22 21:51:29.19257
7	29	f	2025-05-22 21:58:31.50684
7	30	t	2025-05-26 16:04:32.943183
7	49	t	2025-05-26 16:38:38.71787
8	26	t	2025-05-26 16:56:40.31793
6	42	t	2025-05-26 16:59:08.365498
6	48	t	2025-05-26 17:09:44.050954
6	54	t	2025-05-26 17:10:27.813412
6	55	t	2025-05-26 17:16:45.000592
6	28	t	2025-05-26 17:19:42.696953
6	56	t	2025-05-26 17:24:36.641037
4	28	t	2025-05-26 17:30:17.003281
4	55	t	2025-05-26 19:12:19.581386
4	56	t	2025-05-26 19:12:21.863107
4	48	t	2025-05-26 19:12:22.81579
4	54	t	2025-05-26 19:12:24.010717
4	42	t	2025-05-27 08:58:56.43503
7	56	f	2025-05-28 22:22:23.421205
7	42	t	2025-05-28 22:27:12.535966
15	29	t	2025-06-03 22:23:37.169015
15	19	t	2025-06-03 22:23:37.545713
\.


--
-- Data for Name: utenti; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.utenti (id, nome, nickname, email, data_di_nascita, password) FROM stdin;
1	Anna Bianchi	abianchi	anna.bianchi@example.com	1990-04-10	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
2	Luca Verdi	lverdi	luca.verdi@example.com	1988-11-22	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
3	Giulia Neri	gneri	giulia.neri@example.com	1992-07-30	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
4	Marco Rossi	mrossi	marco.rossi@xample.com	1997-05-15	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
5	Sara Gallo	sgallo	s.gallo@xampre.com	1995-12-01	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
6	Francesco Esposito	fesposito	fra@gmail.com	1993-03-20	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
8	Alessia Desideri	ale_desi	ale.desi@gmail.com	2003-06-12	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
9	betta	betta	b@e.it	20003-04-17	$2y$10$NJqjqSh11tCmFi0Vv0cPa.B46Gp4VSzLuCcQQ9/eUK2Yf2VN6IbBS
10	Alessia Benvenuto	Alida	ale@ida.it	2003-08-12	$2y$10$8/J7JIDpiZdguIpvOyebyeVzZ4yBcF01sZDivae8VMFYHhlSav3u6
11	Marco	marcolino	m@a.it	2003-10-12	$2y$10$ICNYFeZoo8j58.GgqAutSO0lFrzphl.9YV8UKo0pV4277LKt7HaQi
12	Mario	mariodesi	mario@desi.it	1968-01-30	$2y$10$49PBPzrFJSJm.J4Iu7DgLOsZyUFsENs/nUL2QVtNiwqGVOC6lw8UG
13	Mario	mariodeside	mario@icloud.it	1968-01-30	$2y$10$7RsaQXT2Co780RKbF0ghK.NLX9TGvG15MStSOCkFnNQ0mryrdiC1m
14	Lorenzo	bebè	lorenzosdf@gmail.com	2025-05-16	$2y$10$3rDQi4JSN37SpI6jxUFxd.iggHwx06/1frZmwOc818dWjzB0E0vqK
7	Ida Benvenuto	ida_b	ida@ida.it	2003-08-19	$2y$10$YOBplovcQqaV/gl3QOED3OI8gVU3oyAUYpMVcxz0E.L.Lwk386Uke
15	Giusy	b_giusy	benvenutogiusy8@gmail.com	2000-12-08	$2y$10$z.vsyhMXy3dfKU0q8EWMIe6zqGpxDRt9a5AwamdNAgNdmZA/eUfKa
\.


--
-- Data for Name: viaggi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.viaggi (id, user_id, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio, lingua, compagnia, descrizione, foto, latitudine, longitudine) FROM stdin;
10	9	New York	2025-05-05	2025-05-09	1000	ristoranti	\N	singolo	Viaggio divertente	/uploads/68192209a1ed8_io.jpg	40.71272810	-74.00601520
9	7	roma	2025-05-17	2025-05-21	400	musei	\N	gruppo	sogno di vedere il Colosseo	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	41.89332030	12.48293210
8	8	Portogallo	2024-05-01	2024-05-15	1800 EUR	spiaggia	Portoghese	coppia	Esplorazione delle città storiche del Portogallo.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	39.39990000	-8.22450000
7	7	Canada	2024-04-10	2024-04-25	2200 EUR	ristoranti	Inglese/French	gruppo	Relax tra le montagne canadesi.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	56.13040000	-106.34680000
6	6	USA	2024-03-15	2024-03-30	3500 EUR	musei	Inglese	coppia	Viaggio alla scoperta delle principali città americane.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	37.09020000	-95.71290000
5	5	Australia	2024-02-01	2024-02-20	3000 EUR	musei	Inglese	gruppo	Tour dei parchi nazionali australiani.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	-25.27440000	133.77510000
4	4	Thailandia	2023-12-05	2023-12-20	2500 EUR	natura	Thai	gruppo	Un viaggio esplorativo alla scoperta delle isole della Thailandia.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	15.87000000	100.99250000
3	3	Francia	2023-07-01	2023-07-10	1200 EUR	ristoranti	Francese	coppia	Escursione tra le Alpi francesi e Parigi.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	46.60340000	1.88830000
2	2	Spagna	2023-06-15	2023-06-25	1500 EUR	spiaggia	Spagnolo	gruppo	Vacanza estiva in Spagna con amici.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	40.46370000	-3.74920000
1	1	Giappone	2023-09-10	2023-09-25	2000 EUR	natura	Giapponese	coppia	Un viaggio indimenticabile tra la tradizione e la modernità.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	35.67620000	139.65030000
12	8	Roma	2025-05-21	2025-05-25	100	natura	\N	gruppo	Emozionante avventura	/uploads/681e2d5d6c1f4_io.jpg	41.89332030	12.48293210
13	8	Roma	2025-05-10	2025-05-25	250	natura	\N	gruppo	Non vedo l'ora di vedere il nuovo Papam!	/uploads/681e348f7b26b_Screenshot 2025-02-07 164924.png	41.89332030	12.48293210
14	4	Giappone	2025-08-31	2025-09-20	3000	spiaggia	\N	coppia	Esplorare la cultura giapponese è il mio sogno!	/uploads/681e3836d602e_Screenshot 2024-10-11 131041.png	36.57484410	139.23941790
15	7	Giappone	2025-04-11	2025-05-13	3000	musei	\N	gruppo	Viaggio indimenticabile	/uploads/6823ae2b16159_Screenshot 2025-05-13 223922.png	36.57484410	139.23941790
16	8	Portogallo	2025-05-11	2025-05-13	1000	spiaggia	\N	gruppo	Weekend all'insegna della festa!	/uploads/6823af5d46a29_Screenshot 2025-05-13 222739.png	39.66216480	-8.13535190
17	8	Giappone	2025-05-07	2025-05-14	3000	natura	\N	gruppo	Viaggio in famiglia	/uploads/6824ca17a1ddf_Screenshot 2025-05-13 223922.png	36.57484410	139.23941790
18	4	Tokyo	2025-05-01	2025-05-14	4000	natura	\N	gruppo	esplorazione di Tokyo	/uploads/6824cd24a1171_Screenshot 2025-05-14 190425.png	35.67686010	139.76389470
19	6	Berlino	2025-06-01	2025-06-10	900	musei	Tedesco	singolo	Scoprire la storia e i musei di Berlino.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	52.52000660	13.40495400
20	5	Atene	2025-07-15	2025-07-22	1100	natura	Greco	gruppo	Alla scoperta delle meraviglie dell'antica Grecia.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	37.98381000	23.72753900
21	3	Reykjavík	2025-03-05	2025-03-12	2000	natura	Inglese	coppia	Aurora boreale e paesaggi mozzafiato in Islanda.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	64.12652100	-21.81743900
22	2	Venezia	2025-09-18	2025-09-23	500	ristoranti	Italiano	singolo	Giro in gondola e cicchetti veneziani.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	45.44084740	12.31551510
23	9	Lisbona	2025-10-05	2025-10-12	700	spiaggia	Portoghese	gruppo	Relax, musica e cultura in Portogallo.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	38.71690000	-9.13990000
24	7	Bali	2025-08-01	2025-08-20	2200	natura	Indonesiano	coppia	Paradiso tropicale e templi sacri.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	-8.34050000	115.09200000
25	1	Marrakech	2025-11-10	2025-11-20	1000	musei	Arabo	singolo	Esplorazione dei souk e dei giardini marocchini.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	31.62950000	-7.98110000
26	6	Barcellona	2025-04-20	2025-04-25	600	spiaggia	Spagnolo	gruppo	Feste, spiaggia e arte con gli amici.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	41.38506390	2.17340350
27	4	Praga	2025-12-01	2025-12-08	800	musei	Ceco	coppia	Atmosfera magica nei mercatini di Natale.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	50.07553810	14.43780050
28	8	Amsterdam	2025-05-26	2025-06-01	950	ristoranti	Olandese	gruppo	Cibo, canali e musei moderni.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	52.36757340	4.90413890
29	2	Parigi	2025-06-10	2025-06-17	1200	musei	Francese	coppia	Un viaggio romantico tra arte e cucina francese.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	48.85661400	2.35222190
30	3	Londra	2025-07-05	2025-07-12	1300	musei	inglese	singolo	Tra pub storici e mostre affascinanti.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	51.50735090	-0.12775830
31	5	Cracovia	2025-08-10	2025-08-17	800	natura	Polacco	gruppo	Un viaggio tra natura e storia in Polonia.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	50.06465010	19.94497990
32	6	Dublino	2025-07-01	2025-07-07	900	ristoranti	Irlandese	coppia	Esplorare la cultura irlandese tra pub e castelli.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	53.34980530	-6.26030970
33	7	Bruxelles	2025-05-18	2025-05-23	850	musei	Francese	gruppo	Arte, cioccolato e birra belga.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	50.85033960	4.35171030
34	8	Napoli	2025-06-20	2025-06-27	700	ristoranti	Italiano	gruppo	Pizza, mare e Vesuvio!	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	40.85179800	14.26812000
35	9	Edimburgo	2025-07-25	2025-08-01	1000	natura	inglese	coppia	Castelli e paesaggi scozzesi mozzafiato.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	55.95325200	-3.18826700
36	10	Malta	2025-09-01	2025-09-08	950	spiaggia	Maltese	gruppo	Sole, mare e cultura nel Mediterraneo.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	35.93749600	14.37541600
37	11	Salonicco	2025-10-05	2025-10-12	850	ristoranti	Greco	gruppo	Cucina greca e tramonti sul Mar Egeo.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	40.64006300	22.94441900
38	3	Oslo	2025-06-15	2025-06-22	1100	natura	Norvegese	singolo	Trekking nei fiordi e panorami mozzafiato.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	59.91386900	10.75224500
39	7	Budapest	2025-05-10	2025-05-17	800	musei	Ungherese	coppia	Bagni termali, musei e romantiche passeggiate sul Danubio.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	47.49791200	19.04023500
40	4	Sarajevo	2025-11-15	2025-11-22	700	musei	Bosniaco	singolo	Tra storia e cultura nella capitale balcanica.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	43.85625860	18.41307630
41	9	Rovaniemi	2025-12-02	2025-12-09	1200	natura	Finlandese	gruppo	Aurora boreale e foreste innevate.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	66.50394700	25.72939000
42	2	Bologna	2025-06-05	2025-06-12	750	ristoranti	Italiano	coppia	Tortellini, portici e cultura emiliana.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	44.49488700	11.34261630
43	5	Lisbona	2025-04-25	2025-05-02	900	musei	Portoghese	gruppo	Tramonti, azulejos e cultura urbana.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	38.71690000	-9.13990000
44	1	Tallinn	2025-08-01	2025-08-08	850	musei	Estone	singolo	Una città medievale ricca di fascino.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	59.43700000	24.75360000
45	6	Palermo	2025-06-28	2025-07-05	800	ristoranti	Italiano	coppia	Tra arancini e mercati storici.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	38.11569000	13.36148600
46	12	Ginevra	2025-07-15	2025-07-22	1100	musei	Francese	gruppo	Scienza, arte e vista sul lago.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	46.20439100	6.14315800
47	10	Reykjavik	2025-09-15	2025-09-22	1300	natura	Islandese	singolo	Geiser, cascate e paesaggi lunari.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	64.14660000	-21.94260000
48	8	Porto	2025-05-30	2025-06-06	850	ristoranti	Portoghese	gruppo	Vino, cucina tradizionale e vista sull’oceano.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	41.15794380	-8.62910530
49	3	Copenaghen	2025-06-20	2025-06-27	950	musei	Danese	coppia	Cultura, design e gastronomia danese.	https://i.pinimg.com/736x/2c/32/50/2c3250a76a0699201d664d86a3611245.jpg	55.67609680	12.56833710
50	2	Stoccolma	2025-07-10	2025-07-17	1000	natura	Svedese	singolo	Isole, architettura e cultura scandinava.	https://i.pinimg.com/736x/7a/22/9d/7a229d5fbdd76b026814465fbbc1b1b4.jpg	59.32932350	18.06858000
51	1	Catania	2025-08-20	2025-08-27	800	musei	Siciliano	gruppo	Cultura, mare e buon cibo.	https://i.pinimg.com/736x/89/08/9c/89089cd5fbe7662e5a35beb13eb18edf.jpg	37.50787700	14.79310600
52	4	Bruxelles	2025-09-15	2025-09-22	900	musei	Belga	coppia	Cultura, birra e cioccolato.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	50.85033960	4.35171030
53	7	Bruxelles	2025-05-02	2025-06-10	900	musei	Belga	coppia	Cultura, birra e cioccolato.	https://i.pinimg.com/736x/08/ee/30/08ee30a9990aea92d1f2a90ea9a35971.jpg	50.85033960	4.35171030
54	7	Perugia	2025-05-29	2025-06-01	300	ristoranti	\N	gruppo	Alla scoperta della cioccolata migliore di'Italia.	/uploads/683480ad934d7_images-2.jpeg	43.10703210	12.40299620
55	7	Oslo	2025-05-31	2025-06-04	400	ristoranti	\N	gruppo	Tra ristoranti stellati e mercatini locali, la capitale norvegese è un vero paradiso per chi ama scoprire nuovi sapori. Dal salmone fresco ai piatti tipici come il “fårikål”, ogni assaggio è un viaggio nella cultura nordica! 🇳🇴✨	/uploads/683485b626df3_Oslo2-800x445.jpg	59.91333010	10.73897010
56	8	Varsavia	2025-05-30	2025-06-04	400	ristoranti	\N	gruppo	Varsavia, una città esuberante, movimentata, piena di vita.	/uploads/683487aa00cbc_images-3.jpeg	52.23371720	21.07143220
\.


--
-- Data for Name: viaggi_terminati; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.viaggi_terminati (id, utente_id, viaggio_id, descrizione, valutazione, foto1, foto2, foto3, foto4, foto5, data_creazione, natura, relax, monumenti, cultura, nightlife) FROM stdin;
3	4	4	bello!	1	/uploads/681b9281d584e_Screenshot_2024-10-10_175841.png	/uploads/681b9281d705b_Screenshot_2024-10-11_140204.png	/uploads/681b9281d7e1a_Screenshot_2024-10-10_175841.png	/uploads/681b9281d8d78_Screenshot_2024-09-30_230444.png	/uploads/681b9281d967e_Screenshot_2024-06-10_110446.png	2025-05-07 19:04:01.892285	0	0	0	0	0
10	9	10	esperienza bellissima insieme ad Aldo!	5	/uploads/681e125d2563c_io.jpg	/uploads/681e125d27203_io.jpg	\N	\N	\N	2025-05-09 16:34:05.161912	0	0	0	0	0
12	8	8	Esperienza migliore della mia vita!	5	/uploads/6823ab7a61cbd_Screenshot_2025-05-13_222739.png	/uploads/6823ab7a62028_Screenshot_2025-05-13_222752.png	\N	\N	\N	2025-05-13 22:28:42.405593	80	50	20	60	10
13	8	4	Paesaggi indimenticabili!	5	/uploads/6823ac922c39e_Screenshot_2025-05-13_223218.png	\N	\N	\N	\N	2025-05-13 22:33:22.18417	10	20	30	40	50
14	7	4	Non lo dimenticherò mai	5	/uploads/6823ad506363d_Screenshot_2025-05-13_223218.png	\N	\N	\N	\N	2025-05-13 22:36:32.408054	10	20	30	40	50
15	7	15	Esperienza bellissima insieme alla mia amica Alessia!	5	/uploads/6823aebd8fd56_Screenshot_2025-05-13_223922.png	\N	\N	\N	\N	2025-05-13 22:42:37.593377	10	20	30	40	50
16	7	16	Paradiso terrestre!	5	/uploads/6823b03c96f5d_Screenshot_2025-05-13_222752.png	\N	\N	\N	\N	2025-05-13 22:49:00.623959	10	20	30	40	50
17	8	17	è stato bellissimo	5	/uploads/6824cb33dc89b_Screenshot_2025-05-13_223922.png	\N	\N	\N	\N	2025-05-14 18:56:19.904421	40	60	10	50	10
19	7	39	"Una città affascinante, divisa in due anime: la storica Buda e la vivace Pest.	4	/uploads/682ede36eebeb_istockphoto-508662108-612x612.jpg	/uploads/682ede36ef23d_images.jpeg	\N	\N	\N	2025-05-22 10:20:06.98022	20	70	20	60	40
22	4	12	Bellissima città, la compagnia non è stata delle migliori	2	/uploads/68348b11b8d75_Roma_in_breve.jpg	/uploads/68348b11b91b5_536216-roman-forum.jpg	\N	\N	\N	2025-05-26 17:38:57.758465	10	10	90	70	30
\.


--
-- Data for Name: viaggi_utenti; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.viaggi_utenti (viaggio_id, user_id, ruolo) FROM stdin;
10	9	partecipante
4	4	partecipante
4	9	partecipante
8	8	partecipante
4	8	partecipante
12	8	ideatore
12	4	partecipante
13	8	ideatore
13	4	partecipante
14	4	ideatore
14	8	partecipante
4	7	partecipante
1	9	partecipante
15	7	ideatore
16	8	ideatore
16	7	partecipante
15	8	partecipante
17	8	ideatore
18	4	ideatore
18	13	partecipante
19	6	partecipante
20	5	partecipante
22	2	ideatore
23	9	ideatore
24	7	ideatore
25	1	ideatore
26	6	ideatore
27	4	ideatore
28	8	ideatore
29	2	partecipante
30	3	ideatore
31	5	partecipante
32	6	partecipante
33	7	partecipante
34	8	partecipante
35	9	partecipante
36	10	ideatore
10	8	partecipante
37	11	ideatore
38	3	ideatore
39	7	ideatore
40	4	ideatore
41	9	ideatore
42	2	ideatore
43	5	ideatore
44	1	ideatore
45	6	ideatore
46	12	ideatore
47	10	ideatore
48	8	ideatore
49	3	ideatore
50	2	ideatore
51	1	ideatore
52	4	ideatore
9	8	partecipante
26	9	partecipante
39	14	partecipante
19	7	partecipante
28	7	partecipante
48	7	partecipante
30	7	partecipante
3	8	partecipante
3	9	partecipante
1	8	partecipante
1	7	partecipante
49	7	partecipante
53	14	partecipante
54	7	ideatore
26	8	partecipante
42	6	partecipante
48	6	partecipante
54	6	partecipante
55	7	ideatore
55	6	partecipante
28	6	partecipante
56	8	ideatore
56	6	partecipante
28	4	partecipante
42	4	partecipante
\.


--
-- Name: chat_viaggio_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.chat_viaggio_id_seq', 33, true);


--
-- Name: esperienze_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.esperienze_id_seq', 2, true);


--
-- Name: itinerari_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.itinerari_id_seq', 11, true);


--
-- Name: notifiche_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.notifiche_id_seq', 563, true);


--
-- Name: utenti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.utenti_id_seq', 15, true);


--
-- Name: viaggi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.viaggi_id_seq', 56, true);


--
-- Name: viaggi_terminati_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.viaggi_terminati_id_seq', 23, true);


--
-- Name: chat_viaggio chat_viaggio_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chat_viaggio
    ADD CONSTRAINT chat_viaggio_pkey PRIMARY KEY (id);


--
-- Name: itinerari itinerari_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.itinerari
    ADD CONSTRAINT itinerari_pkey PRIMARY KEY (id);


--
-- Name: notifiche notifiche_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notifiche
    ADD CONSTRAINT notifiche_pkey PRIMARY KEY (id);


--
-- Name: profili profili_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profili
    ADD CONSTRAINT profili_email_key UNIQUE (email);


--
-- Name: profili profili_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profili
    ADD CONSTRAINT profili_pkey PRIMARY KEY (id);


--
-- Name: swipes unique_swipe; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.swipes
    ADD CONSTRAINT unique_swipe UNIQUE (user_id, trip_id);


--
-- Name: utenti utenti_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_email_key UNIQUE (email);


--
-- Name: utenti utenti_nickname_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_nickname_key UNIQUE (nickname);


--
-- Name: utenti utenti_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_pkey PRIMARY KEY (id);


--
-- Name: viaggi viaggi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viaggi
    ADD CONSTRAINT viaggi_pkey PRIMARY KEY (id);


--
-- Name: viaggi_terminati viaggi_terminati_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viaggi_terminati
    ADD CONSTRAINT viaggi_terminati_pkey PRIMARY KEY (id);


--
-- Name: viaggi_utenti viaggi_utenti_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viaggi_utenti
    ADD CONSTRAINT viaggi_utenti_pkey PRIMARY KEY (viaggio_id, user_id);


--
-- Name: chat_viaggio fk_viaggio; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chat_viaggio
    ADD CONSTRAINT fk_viaggio FOREIGN KEY (viaggio_id) REFERENCES public.viaggi(id) ON DELETE CASCADE;


--
-- Name: preferenze_utente_viaggio preferenze_utente_viaggio_utente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.preferenze_utente_viaggio
    ADD CONSTRAINT preferenze_utente_viaggio_utente_id_fkey FOREIGN KEY (utente_id) REFERENCES public.utenti(id);


--
-- Name: profili profili_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profili
    ADD CONSTRAINT profili_id_fkey FOREIGN KEY (id) REFERENCES public.utenti(id);


--
-- Name: swipes swipes_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.swipes
    ADD CONSTRAINT swipes_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.utenti(id) ON DELETE CASCADE;


--
-- Name: viaggi viaggi_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viaggi
    ADD CONSTRAINT viaggi_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.utenti(id);


--
-- Name: viaggi_utenti viaggi_utenti_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viaggi_utenti
    ADD CONSTRAINT viaggi_utenti_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.utenti(id);


--
-- Name: viaggi_utenti viaggi_utenti_viaggio_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viaggi_utenti
    ADD CONSTRAINT viaggi_utenti_viaggio_id_fkey FOREIGN KEY (viaggio_id) REFERENCES public.viaggi(id);


--
-- PostgreSQL database dump complete
--

