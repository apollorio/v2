<?php

/**
 * Template Part: Head
 * Blank Canvas <head> with all CSS variables and styles.
 *
 * @package Apollo\Sign
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Assinar Documento — Apollo</title>
    <!-- Fonts: Space Grotesk already loaded by CDN core.min.js -->
    <!-- RemixIcon: already loaded by CDN core.min.js -->
    <?php if (defined('APOLLO_SIGN_URL')) : ?>
        <link rel="stylesheet" href="<?php echo esc_url(APOLLO_SIGN_URL . 'assets/css/sign-placement.css'); ?>?v=<?php echo esc_attr(APOLLO_SIGN_VERSION); ?>">
        <link rel="stylesheet" href="<?php echo esc_url(APOLLO_SIGN_URL . 'assets/css/signature-pad.css'); ?>?v=<?php echo esc_attr(APOLLO_SIGN_VERSION); ?>">
    <?php endif; ?>
    <style>
        :root {
            --bg: #0e0e10;
            --sf: #1a1a1e;
            --sf2: #222226;
            --sf3: #2a2a2e;
            --ink: #e8e8ec;
            --muted: #8a8a96;
            --dim: #5c5c68;
            --primary: #f45f00;
            --primary-soft: rgba(244, 95, 0, 0.12);
            --success: #22c55e;
            --success-soft: rgba(34, 197, 94, 0.12);
            --error: #ef4444;
            --error-soft: rgba(239, 68, 68, 0.12);
            --brd: rgba(255, 255, 255, .06);
            --r: 12px;
            --ff: 'Space Grotesk', system-ui, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--ink);
            font-family: var(--ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .sign-container {
            width: 100%;
            max-width: 520px;
            background: var(--sf);
            border: 1px solid var(--brd);
            border-radius: 20px;
            overflow: hidden;
        }

        /* When PDF viewer is active, expand container */
        .sign-container.has-pdf {
            max-width: 780px;
        }

        .sign-header {
            padding: 24px;
            border-bottom: 1px solid var(--brd);
            text-align: center;
        }

        .sign-logo {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -.02em;
            margin-bottom: 4px;
        }

        .sign-subtitle {
            font-size: 12px;
            color: var(--muted);
        }

        .sign-body {
            padding: 24px;
        }

        .sign-doc-info {
            background: var(--sf2);
            border-radius: var(--r);
            padding: 16px;
            margin-bottom: 20px;
        }

        .sign-doc-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .sign-doc-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sign-doc-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .sign-doc-label {
            color: var(--dim);
        }

        .sign-doc-value {
            color: var(--muted);
            font-weight: 500;
        }

        .sign-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--r);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            width: 100%;
            justify-content: center;
        }

        .sign-status.pending {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .sign-status.signed {
            background: var(--success-soft);
            color: var(--success);
        }

        .sign-status i {
            font-size: 18px;
        }

        .sign-form-group {
            margin-bottom: 14px;
        }

        .sign-form-group label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .sign-form-group input[type="file"],
        .sign-form-group input[type="password"],
        .sign-form-group input[type="text"] {
            width: 100%;
            height: 40px;
            background: var(--sf2);
            border: 1px solid var(--brd);
            border-radius: 8px;
            padding: 0 12px;
            color: var(--ink);
            font-size: 13px;
            font-family: var(--ff);
        }

        .sign-form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .sign-form-group input[type="file"] {
            padding: 8px 12px;
            cursor: pointer;
        }

        .sign-btn {
            width: 100%;
            height: 44px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: var(--ff);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: .2s;
            margin-top: 8px;
        }

        .sign-btn:hover {
            filter: brightness(1.1);
        }

        .sign-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .sign-btn i {
            font-size: 18px;
        }

        .sign-btn-secondary {
            background: var(--sf2);
            color: var(--ink);
            border: 1px solid var(--brd);
        }

        .sign-btn-secondary:hover {
            background: var(--sf3);
        }

        .sign-cert-info {
            background: var(--success-soft);
            border-radius: var(--r);
            padding: 14px;
            margin-top: 16px;
        }

        .sign-cert-info h4 {
            font-size: 13px;
            color: var(--success);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sign-cert-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 3px 0;
            border-bottom: 1px solid rgba(34, 197, 94, .1);
        }

        .sign-cert-row:last-child {
            border-bottom: none;
        }

        .sign-cert-label {
            color: var(--dim);
        }

        .sign-cert-value {
            color: var(--ink);
            font-weight: 500;
        }

        .sign-error {
            background: var(--error-soft);
            color: var(--error);
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            margin-top: 12px;
            display: none;
        }

        .sign-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--brd);
            text-align: center;
        }

        .sign-footer p {
            font-size: 10px;
            color: var(--dim);
            line-height: 1.5;
        }

        .sign-hash {
            font-family: monospace;
            font-size: 9px;
            color: var(--dim);
            word-break: break-all;
            margin-top: 8px;
        }

        .sign-icpbr {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: var(--sf2);
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 11px;
            color: var(--muted);
        }

        .sign-icpbr i {
            font-size: 20px;
            color: var(--success);
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <?php do_action('apollo/seo/head'); ?>
</head>