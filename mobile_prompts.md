# Mobile Prototype Prompts for Talent Boost

Here are the prompts you can use on v0.dev, Claude, or ChatGPT to generate the mobile views for your application "Talent Boost".

## 1. Global Style & Theme

"Create a modern, premium mobile app design for a fintech/talent platform called 'Talent Boost'. Use a color palette with deep purple (#4A148C), vibrant orange (#FF6D00) accents, and plenty of white space. The font should be 'Inter' or 'Poppins'. Rounded corners (20px), subtle shadows, and glassmorphism effects for cards."

## 2. Login / Register Screen

**Prompt:**
"Design a mobile Login and Registration screen.

-   **Login**: Input fields for 'Email or Phone Number' and 'Password'. A 'Forgot Password?' link. A large 'Login' button.
-   **Register**: Steps wizard.
    -   Step 1: Personal Info (First Name, Last Name, Phone (Required), Email (Optional), Address).
    -   Step 2: Security (Password, Cnf Password).
    -   Step 3: Verification (Upload ID Card Recto/Verso with a preview box).
    -   Include 'Terms & Conditions' checkbox.
-   Key element: Switch between Login/Register tabs or links."

## 3. Home Dashboard

**Prompt:**
"Design the main Dashboard screen.

-   **Header**: User Avatar (top left), Notification Bell (top right).
-   **Balance Card**: A premium card showing 'Total Balance' hidden by default (eye icon to toggle), with the currency 'FCFA'.
-   **Quick Actions**: Row of circular icons: 'Depôt', 'Retrait', 'Envoyer', 'Payer'.
-   **Wallet Details**: Display the unique Account Number (e.g., '1234567890') with a copy icon, and a small QR Code icon to 'Show my QR'.
-   **Recent Transactions**: A list of the last 3-5 transactions with icons (arrow up/down), user name, date, and amount (green for + funds, red for - funds)."

## 4. Transfer / Send Money

**Prompt:**
"Design the 'Send Money' flow.

-   **Input**: 'Recipient' field.
    -   It should allow typing an Account Number/Phone OR Scanning a QR Code (prominent 'Scan QR' button inside the field).
-   **Amount**: Large numeric input for the amount.
-   **Confirmation**: A summary card showing Sender, Recipient (resolved name), Amount, and Fees (if any).
-   **Action**: 'Confirm Transfer' slide-to-pay button."

## 5. QR Code Screen

**Prompt:**
"Design a 'My QR Code' modal or screen.

-   Centered, large, scannable QR Code.
-   Underneath: The User's Name and Account Number.
-   Buttons: 'Share Image', 'Copy Link'."

## 6. Transaction History

**Prompt:**
"Design a full 'Transaction History' screen.

-   **Filters**: Tabs for 'All', 'Income', 'Outcome'.
-   **List**: Group transactions by Date (Today, Yesterday, Month).
-   Each list item: Avatar/Initials of the other party, Title (e.g., 'Transfer from Moussa'), Timestamp, and Amount (+/-). Status indicator (Completed, Pending)."

## 7. Profile & Settings

**Prompt:**
"Design a Profile screen.

-   **Header**: Large Profile Picture (editable), Name, Phone Number, Role tag (e.g., 'Vendeur').
-   **Sections**:
    -   'Identity Verification': Status badge (e.g., 'Verified' green check, or 'Pending').
    -   'Security': Change Password, Biometrics toggle.
    -   'App Settings': Language (Français/English), Dark Mode.
    -   'Logout' button (destructive red)."
