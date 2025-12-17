# Admin Panel - AB Bau

## Hyrje në Sistem

**URL (Local):** `http://localhost/ab-bau/admin/login.php`
**URL (Production):** `https://ab-bau-fliesen.de/admin/login.php`

**Fjalëkalimi Default:** `admin123`

⚠️ **Kujdes:** Ndrysho fjalëkalimin pas hyrjes së parë!

## Funksionalitetet

### 1. Dashboard
- Pamje e përgjithshme e statistikave
- Numri i fotove (kryesore/portfolio)
- Numri i shërbimeve
- Numri i reviews (pending/approved)

### 2. Menaxhimi i Galerisë
- Shto fotot e reja
- Cakto llojin (Kryesore ose Portfolio)
- Zhvendos fotot midis kategorive
- Fshi fotot

### 3. Menaxhimi i Shërbimeve
- Shto shërbime të reja
- Ndrysho shërbimet ekzistuese
- Aktivizo/Deaktivizo shërbimet
- Fshi shërbimet

### 4. Menaxhimi i Reviews
- Shiko reviews në pritje
- Aprovo ose refuzo reviews
- Fshi reviews të aprovuara

## Struktura e Skedarëve

```
admin/
├── login.php          # Faqja e login
├── dashboard.php      # Dashboard kryesor
├── gallery.php        # Menaxhimi i galerisë
├── services.php       # Menaxhimi i shërbimeve
├── reviews.php        # Menaxhimi i reviews
├── functions.php      # Funksione helper
├── includes/
│   └── db_connect.php # Lidhja me MySQL
└── logout.php         # Logout

api/
├── get-data.php       # API për lexim të dhënash nga MySQL
└── submit-review.php  # API për dërgim reviews

uploads/               # Folder për fotot e uploaduara
```

**Shënim:** Të dhënat ruhen në MySQL, jo në skedarë JSON.

## Siguria

- Fjalëkalimi ruhet si hash (bcrypt)
- Session management për autentifikim
- Sanitizim i të dhënave
- Validim i inputeve
- Mbrojtje e folderit `data/` me `.htaccess`

## Integrimi me Faqen Publike

Faqja publike lexon të dhënat nga API (që merr të dhëna nga MySQL):
- `api/get-data.php?type=gallery` - Për galerinë
- `api/get-data.php?type=services` - Për shërbimet
- `api/get-data.php?type=reviews` - Për reviews
- `api/get-data.php?type=catalogs` - Për kataloget
- `api/get-data.php?type=portfolio` - Për portofolin
- `api/get-data.php?type=customization` - Për konfigurimin e faqes

## Ndryshimi i Fjalëkalimit

Për të ndryshuar fjalëkalimin, përdor funksionalitetin në dashboard ose direkt në databazë.

## Kujdes

- Backup bëj databazën MySQL para ndryshimeve të mëdha
- Kontrollo permissions për folderin `uploads/`
- Të gjitha të dhënat ruhen në MySQL, jo në skedarë JSON

