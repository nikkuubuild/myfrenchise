# Franchise Dekho --- Design System Tokens

**Standard:** Editorial Sophistication + Enterprise Credibility  
**Compliance Target:** WCAG 2.2 AA (Targeting AAA where possible for long-form reading)

---

## 1. Color Palette & Semantic Tokens

### 1.1 Brand & Neutral Palette

| Token Name | Value | Usage & Pairing Rules |
| :--- | :--- | :--- |
| `--color-ink-primary` | `#172B3A` | Primary typography, headers, dark structural containers. |
| `--color-ink-secondary` | `#334E68` | Secondary body text, subtitles, column headers. Contrast > 7.5:1 on white. |
| `--color-ink-muted` | `#586B7B` | Metadata, helper text, timestamps. Contrast > 4.7:1 on white/paper. |
| `--color-surface-canvas` | `#FFFFFF` | Default clean background for application & forms. |
| `--color-surface-paper` | `#F6F3ED` | Warm paper editorial background for hero, highlights, and contrast bands. |
| `--color-surface-card` | `#FFFFFF` | Structured container surface. |
| `--color-surface-subtle` | `#EFECE6` | Light fill for data tables, table headers, and disabled states. |
| `--color-border-subtle` | `#E5E0D8` | Hairline dividers and quiet component borders. |
| `--color-border-strong` | `#C8C0B4` | Active input borders and emphasized borders. |
| `--color-border-focus` | `#172B3A` | High-contrast 2px focus ring with 2px offset. |

### 1.2 Accent & Brand Accents

| Token Name | Value | Usage & Restriction |
| :--- | :--- | :--- |
| `--color-accent-green` | `#49685B` | Restrained editorial accent, trust badges, secondary actions. (Contrast 5.6:1 on white). |
| `--color-accent-green-bg` | `#EDF3F0` | Light tint for verified status badges and success summaries. |
| `--color-accent-brass` | `#D7B77A` | Highlight rules, decorative accents on dark navy backgrounds ONLY. (Never use as text on white). |
| `--color-accent-navy-light`| `#243F53` | Hover state for navy buttons and links. |

### 1.3 Feedback & Verification Status Tokens

| State | Text Color | Surface Tint | Border Color | Accessibility & Meaning |
| :--- | :--- | :--- | :--- | :--- |
| **Verified / Approved** | `#1B4D3E` | `#EAF4F0` | `#A3D2BE` | Contrast > 7:1. Indicates independently verified data. |
| **Pending / Under Review** | `#7A5200` | `#FFF8EB` | `#E5C578` | Contrast > 5.5:1. In progress; not yet published. |
| **Caveat / Attention** | `#8A4500` | `#FDF4ED` | `#F4B98E` | Contrast > 5.5:1. Disclosed caveats or territory restrictions. |
| **Rejected / Suspended** | `#8B1E1E` | `#FDF1F1` | `#F1AAAA` | Contrast > 6.5:1. Unverified, expired, or disputed. |
| **Informational / Notice** | `#164066` | `#EFF6FC` | `#A8CDED` | Contrast > 7.2:1. Disclaimers and process explanations. |

---

## 2. Typography Scale

### 2.1 Font Families

```css
:root {
  /* Editorial headline typeface for section anchors and article headers */
  --font-serif: "Source Serif 4", "Playfair Display", Georgia, "Times New Roman", serif;
  
  /* Precision UI & Body typeface for high legibility, data tables, and forms */
  --font-sans: "Inter", "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  
  /* Monospace for verification hash, case reference IDs, and timestamps */
  --font-mono: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}
```

### 2.2 Scale & Sizing

| Level | Size | Line Height | Tracking | Recommended Font | Typical Elements |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Display Hero** | `2.75rem` (44px) | `1.15` | `-0.02em` | Serif (600) | Homepage hero lead statement |
| **H1 Title** | `2.125rem` (34px) | `1.2` | `-0.015em` | Serif (600) | Opportunity title, Page title |
| **H2 Section** | `1.625rem` (26px) | `1.25` | `-0.01em` | Serif / Sans (600) | Section headings, Modal headers |
| **H3 Subheading** | `1.25rem` (20px) | `1.35` | `-0.005em` | Sans (600) | Group headings, Step labels |
| **Body Large / Lead** | `1.125rem` (18px) | `1.5` | `0` | Sans (400) | Introduction paragraphs |
| **Body Regular** | `1.0rem` (16px) | `1.55` | `0` | Sans (400) | Standard reading text, inputs |
| **Body Medium** | `1.0rem` (16px) | `1.5` | `0` | Sans (500) | Table body, form labels |
| **Small / Meta** | `0.875rem` (14px) | `1.45` | `0.005em` | Sans (400/500) | Footers, helper text, table meta |
| **Micro / Badge** | `0.75rem` (12px) | `1.35` | `0.02em` | Sans (600 uppercase/bold) | Status badges, category tags |

*Note: No text shall ever be rendered below 12px.*

---

## 3. Spatial System & Geometry

### 3.1 Spacing Scale

- `--space-1`: `4px` (`0.25rem`) — Micro spacing, tight icon padding
- `--space-2`: `8px` (`0.5rem`) — Element gaps, chip padding
- `--space-3`: `12px` (`0.75rem`) — Compact input padding, tag padding
- `--space-4`: `16px` (`1.0rem`) — Standard gutter, regular button padding
- `--space-5`: `20px` (`1.25rem`) — Moderate element separation
- `--space-6`: `24px` (`1.5rem`) — Card padding, form group gap
- `--space-8`: `32px` (`2.0rem`) — Section internal rhythm
- `--space-12`: `48px` (`3.0rem`) — Major section separation
- `--space-16`: `64px` (`4.0rem`) — Editorial section breaks

### 3.2 Border Radius & Restraint

- `--radius-none`: `0px` — Sharp editorial dividers, full-width headers
- `--radius-xs`: `2px` — Badges, subtle tags
- `--radius-sm`: `4px` — Inputs, standard buttons, notification boxes
- `--radius-md`: `6px` — Opportunity cards, comparison panels, dialogs
- `--radius-lg`: `8px` — Maximum permitted radius (no oversized rounded bubbles)

### 3.3 Elevation & Shadows (Restrained)

Avoid generic blur shadows. Use crisp hairline borders complemented by shallow depth:
- `--shadow-hairline`: `0 1px 2px rgba(23, 43, 58, 0.05)`
- `--shadow-card`: `0 2px 4px rgba(23, 43, 58, 0.06), 0 1px 2px rgba(23, 43, 58, 0.04)`
- `--shadow-dropdown`: `0 6px 16px rgba(23, 43, 58, 0.08), 0 2px 4px rgba(23, 43, 58, 0.04)`

---

## 4. Interaction & Accessibility States

1. **Focus Ring:**
   ```css
   :focus-visible {
     outline: 2px solid var(--color-ink-primary);
     outline-offset: 2px;
   }
   ```
2. **Buttons:**
   - **Primary Action (Navy):** Background `#172B3A`, Text `#FFFFFF`. Hover: `#243F53`. Active: `#0F1D27`.
   - **Secondary Action (Warm Outline):** Background transparent, Border 1px solid `#172B3A`, Text `#172B3A`. Hover: `#F6F3ED`.
   - **Ghost Action:** Background transparent, Text `#334E68`. Hover: Text `#172B3A`, Background `#EFECE6`.
3. **Form Fields:**
   - Default: Border 1px solid `#C8C0B4`, Background `#FFFFFF`, Height `44px` (touch-friendly minimum).
   - Hover: Border 1px solid `#172B3A`.
   - Focus: Border 2px solid `#172B3A`, Offset 0.
   - Error: Border 2px solid `#8B1E1E`, Background `#FFF8F8`.

---

## 5. CSS Custom Properties Implementation

This token specification translates directly into the application stylesheet `resources/css/app.css`:

```css
:root {
  --color-ink-primary: #172B3A;
  --color-ink-secondary: #334E68;
  --color-ink-muted: #586B7B;
  --color-surface-canvas: #FFFFFF;
  --color-surface-paper: #F6F3ED;
  --color-surface-subtle: #EFECE6;
  --color-border-subtle: #E5E0D8;
  --color-border-strong: #C8C0B4;
  --color-accent-green: #49685B;
  --color-accent-green-bg: #EDF3F0;
  --color-accent-brass: #D7B77A;
  
  --color-status-verified-text: #1B4D3E;
  --color-status-verified-bg: #EAF4F0;
  --color-status-verified-border: #A3D2BE;
  
  --color-status-pending-text: #7A5200;
  --color-status-pending-bg: #FFF8EB;
  --color-status-pending-border: #E5C578;
  
  --color-status-caveat-text: #8A4500;
  --color-status-caveat-bg: #FDF4ED;
  --color-status-caveat-border: #F4B98E;
  
  --color-status-rejected-text: #8B1E1E;
  --color-status-rejected-bg: #FDF1F1;
  --color-status-rejected-border: #F1AAAA;
  
  --font-serif: "Source Serif 4", Georgia, serif;
  --font-sans: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --font-mono: "JetBrains Mono", Consolas, monospace;
  
  --radius-xs: 2px;
  --radius-sm: 4px;
  --radius-md: 6px;
  --radius-lg: 8px;
}
```
