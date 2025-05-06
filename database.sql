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
-- Name: notifiche; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.notifiche (
    id integer NOT NULL,
    utente_id integer NOT NULL,
    mittente_id integer NOT NULL,
    viaggio_id integer NOT NULL,
    titolo_viaggio character varying(255),
    letta boolean DEFAULT false,
    data_creazione timestamp without time zone DEFAULT CURRENT_TIMESTAMP
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
-- Data for Name: notifiche; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.notifiche (id, utente_id, mittente_id, viaggio_id, titolo_viaggio, letta, data_creazione) FROM stdin;
1	6	3	6	USA	f	2025-05-06 18:13:24.698726
2	7	3	7	Canada	f	2025-05-06 18:13:31.1874
4	8	3	8	Portogallo	f	2025-05-06 18:13:32.168294
6	7	3	9	roma	f	2025-05-06 18:13:33.051304
7	9	3	10	New York	f	2025-05-06 18:13:33.920203
8	1	3	1	Giappone	f	2025-05-06 18:13:43.1925
9	2	3	2	Spagna	f	2025-05-06 18:13:44.024532
10	3	3	3	Francia	f	2025-05-06 18:13:44.74743
15	4	3	4	Thailandia	f	2025-05-06 18:14:31.573169
16	5	3	5	Australia	f	2025-05-06 18:14:32.439308
25	1	7	1	Giappone	f	2025-05-06 18:15:41.050084
26	2	7	2	Spagna	f	2025-05-06 18:15:41.771563
27	3	7	3	Francia	f	2025-05-06 18:15:42.430998
28	4	7	4	Thailandia	f	2025-05-06 18:15:43.105922
29	5	7	5	Australia	f	2025-05-06 18:15:54.126798
30	6	7	6	USA	f	2025-05-06 18:15:54.845781
31	7	7	7	Canada	f	2025-05-06 18:15:55.553242
32	8	7	8	Portogallo	f	2025-05-06 18:15:56.274651
33	7	7	9	roma	f	2025-05-06 18:15:57.013156
34	9	7	10	New York	f	2025-05-06 18:15:58.315254
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
7	ida@ida.it	Ida Benvenuto	22	Studentessa di design e amante della moda. Viaggiare mi ispira moltissimo.	#faf3bfc4	2003-08-19	immagini/default.png	
8	ale@desi@gmail.com	Alessia Desideri	22	Futura architetta e viaggiatrice nel cuore. Amo la cultura e l’arte.	#faf3bfc4	2003-06-12	immagini/default.png	
9	b@e.it	betta	22	mi piace viaggiare 	#fbfbce	20003-04-17	uploads/profilo_681921cace5c3.jpg	50
\.


--
-- Data for Name: swipes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.swipes (user_id, trip_id, is_like, created_at) FROM stdin;
6	1	f	2025-05-06 10:20:21.360614
6	2	t	2025-05-06 10:20:22.655217
6	3	t	2025-05-06 10:20:23.349945
6	4	f	2025-05-06 10:20:24.836068
6	5	t	2025-05-06 10:20:26.475582
6	6	f	2025-05-06 10:20:27.444927
6	7	t	2025-05-06 10:20:29.578356
6	8	f	2025-05-06 10:20:31.229276
6	9	t	2025-05-06 10:20:32.429073
6	10	f	2025-05-06 10:20:34.800827
7	11	t	2025-05-06 10:16:10.728117
2	2	t	2025-05-06 18:13:07.995039
3	4	t	2025-05-06 18:14:31.554559
3	5	t	2025-05-06 18:14:32.431859
3	6	t	2025-05-06 18:14:33.906336
3	7	t	2025-05-06 18:14:35.160837
3	8	t	2025-05-06 18:14:36.415359
3	9	t	2025-05-06 18:14:37.837975
3	10	t	2025-05-06 18:14:38.976876
3	1	t	2025-05-06 18:14:41.419358
3	2	t	2025-05-06 18:14:42.739496
3	3	t	2025-05-06 18:14:44.005545
7	1	t	2025-05-06 18:15:41.034002
7	2	t	2025-05-06 18:15:41.76641
7	3	t	2025-05-06 18:15:42.424231
7	4	t	2025-05-06 18:15:43.100855
7	5	t	2025-05-06 18:15:54.10891
7	6	t	2025-05-06 18:15:54.840394
7	7	t	2025-05-06 18:15:55.547315
7	8	t	2025-05-06 18:15:56.2708
7	9	t	2025-05-06 18:15:57.009689
7	10	t	2025-05-06 18:15:58.309647
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
8	Alessia Desideri	ale_desi	ale@desi@gmail.com	2003-06-12	$2y$10$CqZlGGtWI9TS6gD6dFX8g.IwHmzsn4A7Y1xy7XpwDHFmy6lAkie5q
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
1	1	ideatore
1	2	partecipante
2	2	ideatore
2	4	partecipante
2	3	partecipante
3	3	ideatore
3	4	partecipante
4	4	ideatore
4	5	partecipante
5	5	ideatore
5	6	partecipante
6	6	ideatore
6	7	partecipante
7	7	ideatore
7	8	partecipante
8	8	ideatore
9	7	ideatore
10	9	ideatore
\.


--
-- Name: notifiche_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.notifiche_id_seq', 34, true);


--
-- Name: utenti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.utenti_id_seq', 8, true);


--
-- Name: viaggi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.viaggi_id_seq', 11, true);


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
-- Name: notifiche unica_notifica; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notifiche
    ADD CONSTRAINT unica_notifica UNIQUE (utente_id, viaggio_id, mittente_id);


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

