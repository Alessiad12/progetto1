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
-- Data for Name: chat_viaggio; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.chat_viaggio (id, viaggio_id, utente_id, messaggio, data_creazione) FROM stdin;
1	1	7	fghjk	2025-05-07 10:23:51.619263
2	1	7	ciao	2025-05-07 10:24:03.421753
\.


--
-- Data for Name: notifiche; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.notifiche (utente_id, mittente_id, viaggio_id, titolo_viaggio, letta, data_creazione, tipo, id) FROM stdin;
7	7	9	roma	f	2025-05-06 19:41:13.042183	like	16
1	7	1	Giappone	f	2025-05-06 19:41:35.46645	like	17
1	7	1	Giappone	f	2025-05-06 19:41:35.942619	like	18
2	7	2	Spagna	f	2025-05-06 19:41:36.266931	like	19
3	7	3	Francia	f	2025-05-06 19:41:36.961299	like	20
4	7	4	Thailandia	f	2025-05-06 19:41:37.601098	like	21
4	7	4	Thailandia	f	2025-05-06 19:41:38.198597	like	22
5	7	5	Australia	f	2025-05-06 19:41:38.527258	like	23
5	7	5	Australia	f	2025-05-06 19:41:38.825365	like	24
5	7	5	Australia	f	2025-05-06 19:41:39.1061	like	25
6	7	6	USA	f	2025-05-06 19:41:39.376156	like	26
6	7	6	USA	f	2025-05-06 19:41:39.646403	like	27
6	7	6	USA	f	2025-05-06 19:41:39.941897	like	28
7	7	7	Canada	f	2025-05-06 19:41:40.212629	like	29
7	7	7	Canada	f	2025-05-06 19:41:40.475036	like	30
7	7	7	Canada	f	2025-05-06 19:41:40.745595	like	31
8	7	8	Portogallo	f	2025-05-06 19:41:40.978377	like	32
8	7	8	Portogallo	f	2025-05-06 19:41:41.226021	like	33
8	7	8	Portogallo	f	2025-05-06 19:41:41.423206	like	34
8	7	8	Portogallo	f	2025-05-06 19:41:41.597024	like	35
7	7	9	roma	f	2025-05-06 19:41:41.767579	like	36
7	7	9	roma	f	2025-05-06 19:41:41.936514	like	37
7	7	7	Canada	f	2025-05-06 20:17:10.485029	like	38
7	7	7	Canada	f	2025-05-06 20:17:11.029184	like	39
8	7	8	Portogallo	f	2025-05-06 20:17:11.776908	like	40
1	7	1	Giappone	f	2025-05-06 21:35:17.87023	like	41
1	7	1	Giappone	f	2025-05-06 21:35:18.220518	like	42
2	7	2	Spagna	f	2025-05-06 21:35:18.86269	like	43
2	7	2	Spagna	f	2025-05-06 21:35:19.501237	like	44
3	7	3	Francia	f	2025-05-06 21:35:19.89345	like	45
4	7	4	Thailandia	f	2025-05-06 21:35:34.585829	like	46
5	7	5	Australia	f	2025-05-06 21:35:35.251849	like	47
5	7	5	Australia	f	2025-05-06 21:35:35.72979	like	48
6	7	6	USA	f	2025-05-06 21:35:36.076783	like	49
6	7	6	USA	f	2025-05-06 21:35:36.590088	like	50
7	7	7	Canada	f	2025-05-06 21:35:36.929764	like	51
7	7	7	Canada	f	2025-05-06 21:35:37.432468	like	52
8	7	8	Portogallo	f	2025-05-06 21:35:37.780581	like	53
7	7	9	roma	f	2025-05-06 21:35:39.094822	like	54
1	9	1	Giappone	f	2025-05-06 21:59:31.548711	like	66
3	9	3	Francia	f	2025-05-06 21:59:33.537087	like	67
4	9	4	Thailandia	f	2025-05-06 21:59:34.2608	like	68
5	9	5	Australia	f	2025-05-06 21:59:35.01355	like	69
7	9	7	Canada	f	2025-05-06 21:59:36.478881	like	70
8	9	8	Portogallo	f	2025-05-06 21:59:37.120253	like	71
8	9	8	Portogallo	f	2025-05-06 21:59:37.738359	like	72
7	9	9	roma	f	2025-05-06 21:59:38.665115	like	73
1	1	1	Giappone	f	2025-05-07 08:54:57.637703	like	77
1	1	1	Giappone	f	2025-05-07 08:54:57.768903	like	78
2	1	2	Spagna	f	2025-05-07 08:54:58.873269	like	79
4	1	4	Thailandia	f	2025-05-07 08:55:00.393866	like	80
4	1	4	Thailandia	f	2025-05-07 08:55:01.00329	like	81
5	1	5	Australia	f	2025-05-07 08:55:02.526452	like	82
6	1	6	USA	f	2025-05-07 08:55:03.997502	like	83
6	1	6	USA	f	2025-05-07 08:55:04.022556	like	84
8	1	8	Portogallo	f	2025-05-07 08:55:06.191965	like	85
8	1	8	Portogallo	f	2025-05-07 08:55:06.216149	like	86
1	7	1	Giappone	f	2025-05-07 09:03:26.284294	like	90
1	7	1	Giappone	f	2025-05-07 09:41:00.467452	like	91
1	7	1	Giappone	f	2025-05-07 09:41:00.974418	like	92
2	7	2	Spagna	f	2025-05-07 09:41:01.285746	like	93
2	7	2	Spagna	f	2025-05-07 09:41:01.759363	like	94
3	7	3	Francia	f	2025-05-07 09:41:02.01905	like	95
3	7	3	Francia	f	2025-05-07 09:41:02.48135	like	96
4	7	4	Thailandia	f	2025-05-07 09:41:02.666088	like	97
4	7	4	Thailandia	f	2025-05-07 09:41:03.130599	like	98
5	7	5	Australia	f	2025-05-07 09:41:03.693571	like	99
6	7	6	USA	f	2025-05-07 09:41:04.435927	like	100
6	7	6	USA	f	2025-05-07 09:41:05.017327	like	101
7	7	7	Canada	f	2025-05-07 09:41:05.409163	like	102
7	7	7	Canada	f	2025-05-07 09:41:05.977431	like	103
8	7	8	Portogallo	f	2025-05-07 09:41:06.359053	like	104
8	7	8	Portogallo	f	2025-05-07 09:41:06.76679	like	105
7	7	9	roma	f	2025-05-07 09:41:07.089512	like	106
7	7	9	roma	f	2025-05-07 09:41:07.418131	like	107
9	7	10	New York	f	2025-05-07 09:41:07.931788	like	108
1	9	1	Giappone	f	2025-05-07 10:53:08.059201	like	111
1	9	1	Giappone	f	2025-05-07 10:53:08.376829	like	112
1	9	1	Giappone	f	2025-05-07 10:53:08.696896	like	113
2	9	2	Spagna	f	2025-05-07 10:53:09.33838	like	114
3	9	3	Francia	f	2025-05-07 10:53:10.103058	like	115
4	9	4	Thailandia	f	2025-05-07 10:53:10.743807	like	116
4	9	4	Thailandia	f	2025-05-07 10:53:11.225608	like	117
5	9	5	Australia	f	2025-05-07 10:53:11.561636	like	118
5	9	5	Australia	f	2025-05-07 10:53:12.032396	like	119
6	9	6	USA	f	2025-05-07 10:53:12.349	like	120
6	9	6	USA	f	2025-05-07 10:53:12.82434	like	121
7	9	7	Canada	f	2025-05-07 10:53:13.021627	like	122
7	9	7	Canada	f	2025-05-07 10:53:13.470961	like	123
8	9	8	Portogallo	f	2025-05-07 10:53:13.677776	like	124
8	9	8	Portogallo	f	2025-05-07 10:53:14.127671	like	125
7	9	9	roma	f	2025-05-07 10:53:14.324534	like	126
7	9	9	roma	f	2025-05-07 10:53:14.742822	like	127
7	9	9	roma	f	2025-05-07 10:53:14.946575	like	128
9	9	10	New York	f	2025-05-07 10:53:15.557265	like	129

\.


--
-- Data for Name: preferenze_utente_viaggio; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.preferenze_utente_viaggio (utente_id, email, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio, compagnia) FROM stdin;
1	anna.bianchi@example.com	Parigi	2025-06-01	2025-06-10	500-1000€	Culturale	Amici
2	luca.verdi@example.com	New York	2025-07-15	2025-07-25	2000-3000€	Avventura	Famiglia
3	giulia.neri@example.com	Tokyo	2025-09-01	2025-09-15	3000-4000€	Gastronomico	Gruppo
4	marco.rossi@example.com	Roma	2025-05-20	2025-05-25	100-500€	Spiaggia	Coppia
5	s.gallo@example.com	Londra	2025-08-10	2025-08-20	1000-2000€	Shopping	Amici
6	fra@gmail.com	Parigi	2025-06-01	2025-06-10	500-1000€	Culturale	Amici
7	ida@ida.it	Portogallo	2025-07-15	2025-07-25	2000-3000€	Avventura	Famiglia
8	ale.desi@gmail.com	Giappone	2025-09-01	2025-09-15	3000-4000€	Gastronomico	Gruppo
\.


--
-- Data for Name: profili; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.profili (id, email, nome, eta, bio, colore_sfondo, data_di_nascita, immagine_profilo, posizione_immagine) FROM stdin;
1	anna.bianchi@example.com	Anna Bianchi	35	Mi piace viaggiare e scoprire nuovi luoghi. Appassionata di fotografia.	#faf3bfc4	1990-04-10	immagini/default.png	
2	luca.verdi@example.com	Luca Verdi	36	Adoro la musica e il buon cibo. Viaggio spesso per lavoro.	#faf3bfc4	1988-11-22	immagini/default.png	
3	giulia.neri@example.com	Giulia Neri	33	Sono un’appassionata di sport e natura. Mi piace fare trekking.	#faf3bfc4	1992-07-30	immagini/default.png	
4	marco.rossi@xample.com	Marco Rossi	28	Viaggiare è la mia passione. Ho una collezione di mappe antiche.	#faf3bfc4	1997-05-15	immagini/default.png	
5	s.gallo@xampre.com	Sara Gallo	29	Viaggiare è una delle cose che mi rende felice, ma amo anche il buon cinema.	#faf3bfc4	1995-12-01	immagini/default.png	
6	fra@gmail.com	Francesco Esposito	32	Tecnologia e viaggi, la mia vita in poche parole. Sempre in cerca di avventure.	#faf3bfc4	1993-03-20	immagini/default.png	
8	ale.desi@gmail.com	Alessia Desideri	22	Futura architetta e viaggiatrice nel cuore. Amo la cultura e l’arte.	#faf3bfc4	2003-06-12	immagini/default.png	
9	b@e.it	betta	22	mi piace viaggiare 	#fbfbce	20003-04-17	uploads/profilo_681921cace5c3.jpg	50
7	ida@ida.it	Ida Benvenuto	22	Studentessa di design e amante della moda. Viaggiare mi ispira moltissimo.	#fbfbce	2003-08-19	uploads/profilo_681a409cd4722.jpg	50
\.


--
-- Data for Name: swipes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.swipes (user_id, trip_id, is_like, created_at) FROM stdin;

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
7	Ida Benvenuto	ida_b	ida@ida.it	2003-08-19	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
8	Alessia Desideri	ale_desi	ale.desi@gmail.com	2003-06-12	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
9	betta	betta	b@e.it	20003-04-17	$2y$10$NJqjqSh11tCmFi0Vv0cPa.B46Gp4VSzLuCcQQ9/eUK2Yf2VN6IbBS
\.


--
-- Data for Name: viaggi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.viaggi (id, user_id, destinazione, data_partenza, data_ritorno, budget, tipo_viaggio, lingua, compagnia, descrizione, foto, latitudine, longitudine) FROM stdin;
1	1	Giappone	2023-09-10	2023-09-25	2000 EUR	natura	Giapponese	coppia	Un viaggio indimenticabile tra la tradizione e la modernità.		35.67620000	139.65030000
2	2	Spagna	2023-06-15	2023-06-25	1500 EUR	spiaggia	Spagnolo	gruppo	Vacanza estiva in Spagna con amici.		40.46370000	-3.74920000
3	3	Francia	2023-07-01	2023-07-10	1200 EUR	ristoranti	Francese	coppia	Escursione tra le Alpi francesi e Parigi.		46.60340000	1.88830000
4	4	Thailandia	2023-12-05	2023-12-20	2500 EUR	natura	Thai	gruppo	Un viaggio esplorativo alla scoperta delle isole della Thailandia.		15.87000000	100.99250000
5	5	Australia	2024-02-01	2024-02-20	3000 EUR	musei	Inglese	gruppo	Tour dei parchi nazionali australiani.		-25.27440000	133.77510000
6	6	USA	2024-03-15	2024-03-30	3500 EUR	musei	Inglese	coppia	Viaggio alla scoperta delle principali città americane.		37.09020000	-95.71290000
7	7	Canada	2024-04-10	2024-04-25	2200 EUR	ristoranti	Inglese/French	gruppo	Relax tra le montagne canadesi.		56.13040000	-106.34680000
8	8	Portogallo	2024-05-01	2024-05-15	1800 EUR	spiaggia	Portoghese	coppia	Esplorazione delle città storiche del Portogallo.		39.39990000	-8.22450000
9	7	roma	2025-05-17	2025-05-21	400	musei	\N	gruppo	sogno di vedere il Colosseo	\N	41.89332030	12.48293210
10	9	New York	2025-05-05	2025-05-09	1000	ristoranti	\N	singolo	Viaggio divertente	/uploads/68192209a1ed8_io.jpg	40.71272810	-74.00601520
\.


--
-- Data for Name: viaggi_utenti; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.viaggi_utenti (viaggio_id, user_id, ruolo) FROM stdin;

\.


--
-- Name: chat_viaggio_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.chat_viaggio_id_seq', 2, true);


--
-- Name: notifiche_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.notifiche_id_seq', 131, true);


--
-- Name: utenti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.utenti_id_seq', 8, true);


--
-- Name: viaggi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.viaggi_id_seq', 11, true);


--
-- Name: chat_viaggio chat_viaggio_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chat_viaggio
    ADD CONSTRAINT chat_viaggio_pkey PRIMARY KEY (id);


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

