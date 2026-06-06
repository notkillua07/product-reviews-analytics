# Renalsight — Favicon & Web Assets

## File List

| File | Size | Usage |
|------|------|-------|
| `favicon.ico` | 16/32/48/256px | Browser tab (all browsers) |
| `favicon-16x16.png` | 16×16 | Browser tab fallback |
| `favicon-32x32.png` | 32×32 | Browser tab (Retina) |
| `favicon-48x48.png` | 48×48 | Windows taskbar |
| `favicon-96x96.png` | 96×96 | Google TV / high-DPI |
| `apple-touch-icon.png` | 180×180 | iOS home screen bookmark |
| `android-chrome-192x192.png` | 192×192 | Android home screen |
| `android-chrome-512x512.png` | 512×512 | Android splash screen |
| `mstile-70x70.png` | 70×70 | Windows small tile |
| `mstile-150x150.png` | 150×150 | Windows medium tile |
| `mstile-310x310.png` | 310×310 | Windows large tile |
| `og-image.png` | 1200×630 | Social media sharing card |
| `site.webmanifest` | — | PWA / Android manifest |
| `browserconfig.xml` | — | Windows tile config |
| `favicon-head.blade.php` | — | Laravel Blade snippet |

---

## Laravel Setup

### Step 1 — Copy files to `public/`
```bash
cp favicon.ico public/
cp favicon-*.png public/
cp apple-touch-icon.png public/
cp android-chrome-*.png public/
cp mstile-*.png public/
cp og-image.png public/
cp site.webmanifest public/
cp browserconfig.xml public/
```

### Step 2 — Include in your layout
Open `resources/views/layouts/app.blade.php` and paste the contents of
`favicon-head.blade.php` inside your `<head>` tag (after `<meta charset>` and
`<meta name="viewport">`).

### Step 3 — Optional: per-page OG data
In any Blade view, set these before extending the layout:
```blade
@php
    $title = 'Dashboard — Renalsight';
    $description = 'Analyse customer reviews with AI-powered insights.';
@endphp
```

---

## Brand Colors
| Purpose | Hex |
|---------|-----|
| Primary Purple (gradient start) | `#A855F7` |
| Deep Purple (gradient end) | `#5B21B6` |
| Dark Navy (text) | `#1E1B4B` |
| Theme / Tile color | `#6A0DAD` |
