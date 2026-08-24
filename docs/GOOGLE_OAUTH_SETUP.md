# Google Cloud OAuth 2.0 Setup Guide — ShishaRent

This document provides step-by-step instructions for configuring **Google OAuth 2.0 Authentication** for ShishaRent customers and administrators.

---

## 1. Create a Google Cloud Project

1. Navigate to the [Google Cloud Console](https://console.cloud.google.com/).
2. Click the **Project Selector** dropdown at the top left and click **New Project**.
3. Set the **Project Name** to `ShishaRent Platform` (or your preferred name).
4. Click **Create** and ensure the project is selected in the console header.

---

## 2. Configure the OAuth Consent Screen

1. In the left navigation menu, go to **APIs & Services** → **OAuth consent screen**.
2. Select **External** user type and click **Create**.
3. Fill in the required application details:
   - **App Name**: `ShishaRent`
   - **User Support Email**: Select your Google account email or `support@shisharent.com`
   - **App Logo**: Upload the ShishaRent logo (optional for development)
   - **Application Home Page**: `http://localhost:8080` (or `https://shisharent.com` in production)
   - **Developer Contact Information**: Your developer email address
4. Click **Save and Continue**.
5. Under **Scopes**, click **Add or Remove Scopes** and select:
   - `.../auth/userinfo.email` (See your primary Google Account email address)
   - `.../auth/userinfo.profile` (See your personal info, including name and photo)
   - `openid` (Associate you with your personal info on Google)
6. Click **Save and Continue**.
7. Under **Test Users** (while in testing mode), add your test email addresses:
   - E.g., `admin@shisharent.com`, your personal Gmail address.
8. Click **Save and Continue**.

---

## 3. Create OAuth 2.0 Client Credentials

1. Go to **APIs & Services** → **Credentials**.
2. Click **+ CREATE CREDENTIALS** at the top and choose **OAuth client ID**.
3. Set **Application type** to `Web application`.
4. Set **Name** to `ShishaRent Web Client`.
5. Under **Authorized JavaScript origins**, add:
   - `http://localhost:8080` (WordPress Storefront)
   - `http://localhost:3000` (NestJS Backend)
   - Production domain: `https://shisharent.com` (when deployed)
6. Under **Authorized redirect URIs**, add:
   - `http://localhost:3000/api/auth/google/callback` (Backend OAuth redirect handler)
   - Production URL: `https://api.shisharent.com/api/auth/google/callback` (when deployed)
7. Click **Create**.
8. A modal will appear displaying:
   - **Your Client ID** (e.g., `1234567890-abcdefg.apps.googleusercontent.com`)
   - **Your Client Secret** (e.g., `GOCSPX-abc123xyz456...`)

---

## 4. Add Credentials to Environment Variables

Copy the obtained Client ID and Client Secret into your `.env` file in the project root and `backend/.env`:

```env
# ==============================================================================
# Google OAuth 2.0 Authentication & Admin Allowlist Configuration
# ==============================================================================
GOOGLE_CLIENT_ID=1234567890-abcdefg.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abc123xyz456...
GOOGLE_CALLBACK_URL=http://localhost:3000/api/auth/google/callback

# Comma-separated list of Google accounts authorized to receive ADMIN privileges upon login
ADMIN_GOOGLE_EMAILS=admin@shisharent.com,owner@shisharent.com
```

> [!WARNING]
> Never commit `.env` or paste real Google Client Secrets into public git repositories. The repository `.gitignore` is configured to ignore `.env` files automatically.

---

## 5. Configure Administrator Allowlist Security

ShishaRent enforces **strict role separation**:
- Any regular customer who logs in with Google is assigned the **`CUSTOMER`** role.
- Only Google accounts whose verified email matches the **`ADMIN_GOOGLE_EMAILS`** allowlist are granted the **`ADMIN`** role.
- If an unauthorized user attempts an admin login, the system rejects the attempt with **`403 Forbidden`** (`"You are not authorized to access the administrator account."`).

To grant admin access to specific Google accounts, add their emails (comma-separated, case-insensitive) to `ADMIN_GOOGLE_EMAILS`:

```env
ADMIN_GOOGLE_EMAILS=admin@shisharent.com,lead.operations@shisharent.com
```

---

## 6. How to Test Google Sign-In

### Testing Customer Login:
1. Open the Storefront at `http://localhost:8080/`.
2. Click the **Account** icon in the header navigation or trigger the login modal.
3. Click **Continue with Google**.
4. Log in with your Google Account on the Google OAuth Consent screen.
5. Upon approval, you will be redirected back to the storefront with an authenticated customer session (`JWT` stored securely in local storage).

### Testing Administrator Login:
1. Open the Admin Command Center at `http://localhost:8080/?shisharent_portal=admin`.
2. Click **Sign in with Google**.
3. Log in with an email listed in `ADMIN_GOOGLE_EMAILS`.
4. Upon approval, you will receive full administrator dashboard access.
5. If you log in with a non-allowlisted email, the portal displays:
   `"You are not authorized to access the administrator account."`

---

## 7. Placeholder Mode Handling

Until real Google credentials are added to `.env`:
- The entire backend and frontend build and run without errors.
- Clicking **Continue with Google** will cleanly display:
  `"Google Sign-In is not configured yet. Please configure Google OAuth credentials in backend environment variables."`
- The system will **never** fake a login or generate dummy admin tokens.
