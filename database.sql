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
    data_creazione timestamp without time zone DEFAULT now() NOT NULL
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
5	7	7	ciao	2025-05-07 21:24:51.877276
6	7	7	ciao a te	2025-05-07 21:25:00.614084
\.


--
-- Data for Name: notifiche; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.notifiche (utente_id, mittente_id, viaggio_id, titolo_viaggio, letta, data_creazione, tipo, id) FROM stdin;
1	9	1	Giappone	f	2025-05-07 18:22:20.084211	like	132
2	9	2	Spagna	f	2025-05-07 18:22:21.530452	like	133
3	9	3	Francia	f	2025-05-07 18:22:22.480367	like	134
4	9	4	Thailandia	f	2025-05-07 18:22:27.180749	like	135
4	9	4	Thailandia	f	2025-05-07 18:22:27.278979	like	136
5	9	5	Australia	f	2025-05-07 18:22:29.814438	like	137
5	9	5	Australia	f	2025-05-07 18:22:29.929408	like	138
6	9	6	USA	f	2025-05-07 18:22:31.200953	like	139
6	9	6	USA	f	2025-05-07 18:22:31.288488	like	140
7	9	7	Canada	f	2025-05-07 18:22:32.461536	like	141
7	9	7	Canada	f	2025-05-07 18:22:32.559352	like	142
8	9	8	Portogallo	f	2025-05-07 18:22:33.778138	like	143
8	9	8	Portogallo	f	2025-05-07 18:22:33.876588	like	144
7	9	9	roma	f	2025-05-07 18:22:34.977981	like	145
7	9	9	roma	f	2025-05-07 18:22:35.076783	like	146
9	9	10	New York	f	2025-05-07 18:22:36.17635	like	147
9	9	10	New York	f	2025-05-07 18:22:36.256337	like	148
9	9	10	New York	f	2025-05-07 18:23:01.040967	match_accepted	149
9	9	10	New York	f	2025-05-07 18:23:01.049495	match_accepted	150
1	4	1	Giappone	f	2025-05-07 18:23:27.480237	like	151
1	4	1	Giappone	f	2025-05-07 18:23:28.156126	like	152
2	4	2	Spagna	f	2025-05-07 18:23:28.642372	like	153
1	4	1	Giappone	f	2025-05-07 18:23:51.634913	like	154
1	4	1	Giappone	f	2025-05-07 18:23:52.297721	like	155
2	4	2	Spagna	f	2025-05-07 18:23:52.991906	like	156
2	4	2	Spagna	f	2025-05-07 18:23:53.70412	like	157
3	4	3	Francia	f	2025-05-07 18:23:54.244007	like	158
4	4	4	Thailandia	f	2025-05-07 18:23:55.289357	like	159
5	4	5	Australia	f	2025-05-07 18:23:56.059469	like	160
5	4	5	Australia	f	2025-05-07 18:23:56.155037	like	161
6	4	6	USA	f	2025-05-07 18:23:56.840479	like	162
6	4	6	USA	f	2025-05-07 18:23:57.446206	like	163
7	4	7	Canada	f	2025-05-07 18:23:58.086202	like	164
8	4	8	Portogallo	f	2025-05-07 18:23:58.959136	like	165
7	4	9	roma	f	2025-05-07 18:23:59.845157	like	166
9	4	10	New York	f	2025-05-07 18:24:06.108132	like	167
4	4	4	Thailandia	f	2025-05-07 18:24:30.493777	match_accepted	168
4	4	4	Thailandia	f	2025-05-07 18:24:30.561749	match_accepted	169
4	4	4	Thailandia	f	2025-05-07 18:25:00.564105	registra_viaggio	170
1	4	1	Giappone	f	2025-05-07 18:30:09.172836	like	171
2	4	2	Spagna	f	2025-05-07 18:30:09.959661	like	172
3	4	3	Francia	f	2025-05-07 18:30:11.089584	like	173
4	4	4	Thailandia	f	2025-05-07 18:30:12.871021	like	174
5	4	5	Australia	f	2025-05-07 18:30:14.421107	like	175
6	4	6	USA	f	2025-05-07 18:30:15.871348	like	176
7	4	7	Canada	f	2025-05-07 18:30:17.154726	like	177
8	4	8	Portogallo	f	2025-05-07 18:30:18.704164	like	178
7	4	9	roma	f	2025-05-07 18:30:20.386579	like	179
9	4	10	New York	f	2025-05-07 18:30:21.33781	like	180
4	9	4	Thailandia	f	2025-05-07 18:40:03.544779	match_accepted	181
9	4	4	Thailandia	f	2025-05-07 18:40:03.557505	match_accepted	182
9	4	4	Thailandia	f	2025-05-07 18:41:00.792099	registra_viaggio	183
1	9	1	Giappone	f	2025-05-07 18:42:42.5565	like	184
1	9	1	Giappone	f	2025-05-07 18:42:43.068974	like	185
2	9	2	Spagna	f	2025-05-07 18:42:43.610157	like	186
3	9	3	Francia	f	2025-05-07 18:42:44.779847	like	187
4	9	4	Thailandia	f	2025-05-07 18:42:45.876974	like	188
4	9	4	Thailandia	f	2025-05-07 18:42:46.418983	like	189
5	9	5	Australia	f	2025-05-07 18:42:46.952637	like	190
5	9	5	Australia	f	2025-05-07 18:42:47.569016	like	191
6	9	6	USA	f	2025-05-07 18:42:48.056996	like	192
7	9	7	Canada	f	2025-05-07 18:42:48.859545	like	193
7	9	7	Canada	f	2025-05-07 18:42:49.458182	like	194
8	9	8	Portogallo	f	2025-05-07 18:42:50.146075	like	195
7	9	9	roma	f	2025-05-07 18:42:51.237178	like	196
7	9	9	roma	f	2025-05-07 18:42:51.881267	like	197
1	4	1	Giappone	f	2025-05-07 18:43:37.760885	like	198
1	4	1	Giappone	f	2025-05-07 18:43:38.118688	like	199
1	4	1	Giappone	f	2025-05-07 18:43:38.460513	like	200
2	4	2	Spagna	f	2025-05-07 18:43:38.824704	like	201
2	4	2	Spagna	f	2025-05-07 18:43:39.087405	like	202
2	4	2	Spagna	f	2025-05-07 18:43:39.533265	like	203
1	8	1	Giappone	f	2025-05-07 18:45:15.842825	like	204
2	8	2	Spagna	f	2025-05-07 18:45:16.799747	like	205
3	8	3	Francia	f	2025-05-07 18:45:17.538807	like	206
4	8	4	Thailandia	f	2025-05-07 18:45:23.413518	like	207
5	8	5	Australia	f	2025-05-07 18:45:24.15683	like	208
6	8	6	USA	f	2025-05-07 18:45:24.876548	like	209
6	8	6	USA	f	2025-05-07 18:45:25.581801	like	210
7	8	7	Canada	f	2025-05-07 18:45:26.223731	like	211
7	8	7	Canada	f	2025-05-07 18:45:26.897348	like	212
8	8	8	Portogallo	f	2025-05-07 18:45:27.333088	like	213
7	8	9	roma	f	2025-05-07 18:45:28.423853	like	214
9	8	10	New York	f	2025-05-07 18:45:29.374341	like	215
8	8	8	Portogallo	f	2025-05-07 18:45:47.327914	match_accepted	216
8	8	8	Portogallo	f	2025-05-07 18:45:47.405776	match_accepted	217
8	8	8	Portogallo	f	2025-05-07 18:46:00.792582	registra_viaggio	218
1	8	1	Giappone	f	2025-05-07 18:46:04.966766	like	219
2	8	2	Spagna	f	2025-05-07 18:46:05.692425	like	220
3	8	3	Francia	f	2025-05-07 18:46:06.628202	like	221
4	8	4	Thailandia	f	2025-05-07 18:46:07.706254	like	222
8	8	8	Portogallo	f	2025-05-07 18:46:15.330596	like	223
7	8	9	roma	f	2025-05-07 18:46:15.795622	like	224
2	8	2	Spagna	f	2025-05-07 18:47:09.830309	like	225
3	8	3	Francia	f	2025-05-07 18:47:10.711615	like	226
4	8	4	Thailandia	f	2025-05-07 18:47:11.605765	like	227
5	8	5	Australia	f	2025-05-07 18:47:13.235971	like	228
6	8	6	USA	f	2025-05-07 18:47:14.53851	like	229
7	8	7	Canada	f	2025-05-07 18:47:15.59306	like	230
8	8	8	Portogallo	f	2025-05-07 18:47:16.339098	like	231
7	8	9	roma	f	2025-05-07 18:47:17.317724	like	232
9	8	10	New York	f	2025-05-07 18:47:31.053788	like	233
9	8	10	New York	f	2025-05-07 18:47:31.119628	like	234
1	7	1	Giappone	f	2025-05-07 21:21:52.942965	like	235
1	7	1	Giappone	f	2025-05-07 21:21:53.516862	like	236
2	7	2	Spagna	f	2025-05-07 21:21:53.985995	like	237
3	7	3	Francia	f	2025-05-07 21:21:54.981671	like	238
7	7	7	Canada	f	2025-05-07 21:22:01.484042	like	239
7	7	9	roma	f	2025-05-07 21:22:03.728394	like	240
7	7	9	roma	f	2025-05-07 21:22:12.291717	match_accepted	241
7	7	9	roma	f	2025-05-07 21:22:12.30611	match_accepted	242
7	7	7	Canada	f	2025-05-07 21:22:21.191862	match_accepted	243
7	7	7	Canada	f	2025-05-07 21:22:21.199523	match_accepted	244
7	7	7	Canada	f	2025-05-07 21:25:00.288755	registra_viaggio	245
7	7	9	roma	f	2025-05-07 21:27:44.371706	match_accepted	246
7	7	7	Canada	f	2025-05-07 21:27:47.063476	match_accepted	247
1	3	1	Giappone	f	2025-05-07 21:29:00.083907	like	248
2	3	2	Spagna	f	2025-05-07 21:29:00.898807	like	249
3	3	3	Francia	f	2025-05-07 21:29:01.733857	like	250
4	3	4	Thailandia	f	2025-05-07 21:29:02.815561	like	251
5	3	5	Australia	f	2025-05-07 21:29:03.526985	like	252
6	3	6	USA	f	2025-05-07 21:29:04.225354	like	253
7	3	7	Canada	f	2025-05-07 21:29:05.079597	like	254
8	3	8	Portogallo	f	2025-05-07 21:29:05.723684	like	255
8	3	8	Portogallo	f	2025-05-07 21:29:06.340111	like	256
7	3	9	roma	f	2025-05-07 21:29:06.862889	like	257
9	3	10	New York	f	2025-05-07 21:29:07.668591	like	258
3	3	3	Francia	f	2025-05-07 21:29:17.354781	match_accepted	259
3	3	3	Francia	f	2025-05-07 21:29:17.367659	match_accepted	260
3	3	3	Francia	f	2025-05-07 21:30:00.649823	registra_viaggio	261
1	7	1	Giappone	f	2025-05-07 21:31:16.678467	like	262
2	7	2	Spagna	f	2025-05-07 21:31:17.539604	like	263
3	7	3	Francia	f	2025-05-07 21:31:19.007097	like	264
3	7	3	Francia	f	2025-05-07 21:33:16.509615	match_accepted	265
7	3	3	Francia	f	2025-05-07 21:33:16.558247	match_accepted	266
7	3	9	roma	f	2025-05-07 21:33:26.102082	match_accepted	267
3	7	9	roma	f	2025-05-07 21:33:26.107578	match_accepted	268
7	3	3	Francia	f	2025-05-07 21:35:01.040631	registra_viaggio	269
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
5	s.gallo@xampre.com	Sara Gallo	29	Viaggiare è una delle cose che mi rende felice, ma amo anche il buon cinema.	#faf3bfc4	1995-12-01	immagini/default.png	
6	fra@gmail.com	Francesco Esposito	32	Tecnologia e viaggi, la mia vita in poche parole. Sempre in cerca di avventure.	#faf3bfc4	1993-03-20	immagini/default.png	
7	ida@ida.it	Ida Benvenuto	22	Studentessa di design e amante della moda. Viaggiare mi ispira moltissimo.	#fbfbce	2003-08-19	uploads/profilo_681a409cd4722.jpg	50
9	b@e.it	betta	22		#fbfbce	20003-04-17		50
4	marco.rossi@xample.com	Marco Rossi	28	Viaggiare è la mia passione. Ho una collezione di mappe antiche.	#fbfbce	1997-05-15	uploads/profilo_681b8db67e7dd.png	50
8	ale.desi@gmail.com	Alessia Desideri	22	Futura architetta e viaggiatrice nel cuore. Amo la cultura e l’arte.	#fbe0ce	2003-06-12	uploads/profilo_681b8e190dfe7.png	50
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
9	10	t	2025-05-07 18:22:36.247669
8	10	t	2025-05-07 18:47:31.1144
4	3	t	2025-05-07 18:30:11.081933
4	4	t	2025-05-07 18:30:12.852927
4	5	t	2025-05-07 18:30:14.392535
4	6	t	2025-05-07 18:30:15.841951
4	7	t	2025-05-07 18:30:17.124912
4	8	t	2025-05-07 18:30:18.677941
4	9	t	2025-05-07 18:30:20.368005
4	10	t	2025-05-07 18:30:21.31634
9	1	t	2025-05-07 18:42:43.054485
9	2	t	2025-05-07 18:42:43.587815
9	3	t	2025-05-07 18:42:44.770177
9	4	t	2025-05-07 18:42:46.404924
9	5	t	2025-05-07 18:42:47.54973
9	6	t	2025-05-07 18:42:48.030224
9	7	t	2025-05-07 18:42:49.449382
9	8	t	2025-05-07 18:42:50.138483
9	9	t	2025-05-07 18:42:51.874661
4	1	t	2025-05-07 18:43:38.450547
4	2	t	2025-05-07 18:43:39.512363
7	4	f	2025-05-07 21:21:57.375802
7	5	f	2025-05-07 21:21:58.663308
7	6	f	2025-05-07 21:22:00.151083
7	7	t	2025-05-07 21:22:01.476834
7	8	f	2025-05-07 21:22:02.92277
7	9	t	2025-05-07 21:22:03.720668
3	1	t	2025-05-07 21:29:00.03326
3	2	t	2025-05-07 21:29:00.890828
3	3	t	2025-05-07 21:29:01.726301
3	4	t	2025-05-07 21:29:02.80768
3	5	t	2025-05-07 21:29:03.521454
3	6	t	2025-05-07 21:29:04.220212
3	7	t	2025-05-07 21:29:05.072627
3	8	t	2025-05-07 21:29:06.3356
3	9	t	2025-05-07 21:29:06.856286
3	10	t	2025-05-07 21:29:07.660905
7	1	t	2025-05-07 21:31:16.659287
7	2	t	2025-05-07 21:31:17.533464
7	3	t	2025-05-07 21:31:19.00202
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
-- Data for Name: viaggi_terminati; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.viaggi_terminati (id, utente_id, viaggio_id, descrizione, valutazione, foto1, foto2, foto3, foto4, foto5, data_creazione) FROM stdin;
1	4	4	è stato un viaggio bellissimo !	4	/uploads/681b8c495c1b3_Screenshot 2024-06-10 110446.png	/uploads/681b8c495c843_Screenshot 2024-07-11 222439.png	/uploads/681b8c495cde6_Screenshot 2024-10-11 131041.png	\N	\N	2025-05-07 18:37:29.381941
2	8	8	Bellissima esperienza!	5	\N	\N	\N	\N	\N	2025-05-07 18:46:42.832867
3	4	4	bello!	1	/uploads/681b9281d584e_Screenshot_2024-10-10_175841.png	/uploads/681b9281d705b_Screenshot_2024-10-11_140204.png	/uploads/681b9281d7e1a_Screenshot_2024-10-10_175841.png	/uploads/681b9281d8d78_Screenshot_2024-09-30_230444.png	/uploads/681b9281d967e_Screenshot_2024-06-10_110446.png	2025-05-07 19:04:01.892285
4	4	4	fun!	5	/uploads/681b9350d95e7_Screenshot_2024-06-10_110446.png	/uploads/681b9350d9c9e_Screenshot_2024-07-11_144642.png	\N	/uploads/681b9350da239_Screenshot_2024-07-11_222439.png	/uploads/681b9350da802_Screenshot_2024-07-11_222439.png	2025-05-07 19:07:28.89674
5	4	4	bellooooooo	5	/uploads/681b93cfa0008_Screenshot_2024-10-11_131041.png	/uploads/681b93cfa0764_Screenshot_2024-10-19_105749.png	/uploads/681b93cfa0dc5_Screenshot_2024-10-10_175943.png	/uploads/681b93cfa1354_Screenshot_2024-10-09_110748.png	/uploads/681b93cfa1919_Screenshot_2024-07-11_222439.png	2025-05-07 19:09:35.663309
6	4	4	bellissima, esperienza	5	/uploads/681b9af3e676c_IMG_3307.jpeg	/uploads/681b9af3eccff_IMG_1427.jpeg	\N	\N	\N	2025-05-07 19:40:03.971354
7	3	3	bellissimo	3	/uploads/681bb5b0c84ee_IMG_1056.jpeg	\N	\N	\N	\N	2025-05-07 21:34:08.82162
8	3	3	stupendo	5	/uploads/681bb611647f2_Screenshot_2025-03-21_alle_16.24.04.png	/uploads/681bb61164e01_68192209a1ed8_io.jpg	\N	\N	\N	2025-05-07 21:35:45.413702
9	7	3	bellissimo	5	/uploads/681bc640ed721_Screenshot_2025-03-21_alle_16.24.04.png	\N	\N	\N	\N	2025-05-07 22:44:48.973912
\.


--
-- Data for Name: viaggi_utenti; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.viaggi_utenti (viaggio_id, user_id, ruolo) FROM stdin;
10	9	partecipante
4	4	partecipante
4	9	partecipante
8	8	partecipante
9	7	partecipante
7	7	partecipante
3	3	partecipante
3	7	partecipante
9	3	partecipante
\.


--
-- Name: chat_viaggio_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.chat_viaggio_id_seq', 6, true);


--
-- Name: esperienze_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.esperienze_id_seq', 2, true);


--
-- Name: notifiche_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.notifiche_id_seq', 269, true);


--
-- Name: utenti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.utenti_id_seq', 8, true);


--
-- Name: viaggi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.viaggi_id_seq', 11, true);


--
-- Name: viaggi_terminati_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.viaggi_terminati_id_seq', 9, true);


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

