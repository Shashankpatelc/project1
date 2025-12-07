# Mental health support and stress tracking website

CalmTrack is a small, static single-page app that helps you log stress levels, take short notes, and view your stress trend over time. Data is stored locally in your browser's `localStorage` (no server, no accounts).

Features
- Log stress level (0–10) with mood and optional notes.
- View a line chart of stress levels over time (powered by Chart.js CDN).
- Export your logs as CSV and clear all local data.
- Quick links to mental health resources and crisis contacts.

Files added
- `index.html` — single-page UI (form, chart, entries, resources).
- `styles.css` — styling and responsive layout.
- `app.js` — main JavaScript: localStorage persistence, rendering, export.

How to run
1. Open the project folder and open `index.html` in a browser (double-click or drag into the browser). No server required for local use.
2. Add entries using the "Log Stress" form. Entries are saved to your browser.
3. Use "Export CSV" to save your logs or "Clear All Entries" to remove local data.

Notes and limitations
- This is a demo/static site intended for personal tracking and exploration. It is NOT a replacement for professional mental health services.
- If you're in crisis, contact local emergency services or your local crisis line (e.g., 988 in the United States).

Next steps you might want
- Add server-side storage (authenticated) to sync across devices.
- Add charts by mood, weekly averages, or reminders/notifications.
- Add more accessibility checks and i18n/localization.

License
This repository is provided as-is for personal use and prototyping.

---
Created by project scaffolding script.
# Mental health support and stress tracking website
