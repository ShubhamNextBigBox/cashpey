@extends('layouts.master')
@section('page-title', $page_info['page_title'])
@section('main-section')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #06b6d4;
            --success: #4f46e5;
            --warning: #f59e0b;
            --error: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--white), #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .card {
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="50" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="30" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .card-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .card-body {
            padding: 2rem;
        }

        /* Upload Zone Styles */
        .upload-zone {
            border: 2px dashed var(--gray-300);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1.5rem;
            cursor: pointer;
            background: var(--gray-50);
        }

        .upload-zone:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
            transform: translateY(-2px);
        }

        .upload-zone.dragover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
            transform: scale(1.02);
        }

        .upload-zone.file-selected {
            border-color: var(--success);
            background: rgba(16, 185, 129, 0.05);
        }

        .upload-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
            color: var(--gray-400);
            transition: all 0.3s ease;
        }

        .file-selected .upload-icon {
            color: var(--success);
        }

        .file-info {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
            animation: slideIn 0.3s ease;
        }

        .file-info.show {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .checkbox-container input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 3px;
            accent-color: var(--primary);
        }



        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(99, 102, 241, 0.5);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-block {
            width: 100%;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Progress Bar */
        .progress-container {
            margin-top: 1.5rem;
            display: none;
        }

        .progress-container.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-600);
        }

        .progress-bar {
            height: 8px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            width: 0%;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* COMPLETELY REDESIGNED STATUS SECTION */
        .status-section {
            margin-top: 2rem;
            display: none;
        }

        .status-section.show {
            display: block;
            animation: statusSectionAppear 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes statusSectionAppear {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .status-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .status-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .status-subtitle {
            color: var(--gray-500);
            font-size: 0.875rem;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .status-card {
            background: var(--white);
            border: 2px solid var(--gray-100);
            border-radius: 20px;
            padding: 1.5rem;
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
        }

        .status-card.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gray-200);
            transition: all 0.4s ease;
        }

        .status-card.pending::before {
            background: var(--gray-300);
        }

        .status-card.processing::before {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            animation: progressGlow 2s ease-in-out infinite;
        }

        .status-card.completed::before {
            background: linear-gradient(90deg, var(--success), #34d399);
        }

        @keyframes progressGlow {

            0%,
            100% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }
        }

        .status-card.processing {
            border-color: rgba(99, 102, 241, 0.3);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.02), rgba(6, 182, 212, 0.02));
            box-shadow: 0 8px 32px rgba(99, 102, 241, 0.15);
            transform: translateY(-4px);
        }

        .status-card.completed {
            border-color: rgba(16, 185, 129, 0.3);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.02), rgba(52, 211, 153, 0.02));
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.15);
        }

        .status-icon-container {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            position: relative;
            transition: all 0.4s ease;
        }

        .status-card.pending .status-icon-container {
            background: var(--gray-100);
            color: var(--gray-400);
        }

        .status-card.processing .status-icon-container {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            animation: iconPulse 2s ease-in-out infinite;
        }

        .status-card.completed .status-icon-container {
            background: linear-gradient(135deg, var(--success), #34d399);
            color: white;
            animation: iconBounce 0.6s ease-out;
        }

        @keyframes iconPulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(99, 102, 241, 0);
            }
        }

        @keyframes iconBounce {
            0% {
                transform: scale(0.8);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .status-icon {
            width: 28px;
            height: 28px;
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-card.processing .status-icon {
            animation: iconSpin 2s linear infinite;
        }

        @keyframes iconSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .status-content h3 {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--gray-800);
        }

        .status-card.processing .status-content h3 {
            color: var(--primary);
        }

        .status-card.completed .status-content h3 {
            color: var(--success);
        }

        .status-content p {
            font-size: 0.875rem;
            color: var(--gray-500);
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .status-details {
            background: var(--gray-50);
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.8rem;
            color: var(--gray-600);
            display: none;
        }

        .status-card.processing .status-details,
        .status-card.completed .status-details {
            display: block;
            animation: detailsSlideIn 0.3s ease;
        }

        @keyframes detailsSlideIn {
            from {
                opacity: 0;
                height: 0;
            }

            to {
                opacity: 1;
                height: auto;
            }
        }

        .status-card.processing .status-details {
            background: rgba(99, 102, 241, 0.05);
            color: var(--primary);
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .status-card.completed .status-details {
            background: rgba(16, 185, 129, 0.05);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.1);
        }

        .processing-dots {
            display: inline-flex;
            gap: 4px;
            margin-left: 8px;
        }

        .processing-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--primary);
            animation: dotPulse 1.5s ease-in-out infinite;
        }

        .processing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .processing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes dotPulse {

            0%,
            60%,
            100% {
                transform: scale(1);
                opacity: 0.7;
            }

            30% {
                transform: scale(1.4);
                opacity: 1;
            }
        }

        /* Overall Progress Indicator */
        .overall-progress {
            background: var(--white);
            border: 2px solid var(--gray-100);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }

        .overall-progress h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 1rem;
        }

        .circular-progress {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
        }

        .circular-progress svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .circular-progress .bg-circle {
            fill: none;
            stroke: var(--gray-200);
            stroke-width: 6;
        }

        .circular-progress .progress-circle {
            fill: none;
            stroke: url(#progressGradient);
            stroke-width: 6;
            stroke-linecap: round;
            stroke-dasharray: 251.2;
            stroke-dashoffset: 251.2;
            transition: stroke-dashoffset 0.5s ease;
        }

        .progress-percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-700);
        }

        /* Download Section */
        .download-section {
            margin-top: 2rem;
            padding: 2rem;
            background: linear-gradient(135deg, var(--success), #34d399);
            border-radius: 20px;
            text-align: center;
            color: white;
            display: none;
            position: relative;
            overflow: hidden;
        }

        .download-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" opacity="0.1"/><circle cx="80" cy="80" r="2" fill="white" opacity="0.1"/><circle cx="40" cy="60" r="1" fill="white" opacity="0.1"/><circle cx="60" cy="40" r="1" fill="white" opacity="0.1"/></svg>');
        }

        .download-section.show {
            display: block;
            animation: celebrationEntry 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes celebrationEntry {
            0% {
                opacity: 0;
                transform: scale(0.8) translateY(40px);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05) translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .download-section h3 {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .download-section p {
            margin-bottom: 1.5rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .btn-download {
            background: white;
            color: var(--success);
            border: none;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .btn-download:hover {
            background: var(--gray-50);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Error Message */
        .error-message {
            padding: 1rem;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            color: var(--error);
            margin-top: 1rem;
            display: none;
        }

        .error-message.show {
            display: block;
            animation: errorSlideIn 0.3s ease;
        }

        @keyframes errorSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* File Preview */
        .file-preview {
            margin-top: 1.5rem;
            display: none;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            overflow: hidden;
        }

        .file-preview.show {
            display: block;
            animation: slideIn 0.3s ease;
        }

        .file-preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }

        .file-preview-toggle {
            background: none;
            border: none;
            color: var(--gray-600);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .file-preview-toggle:hover {
            color: var(--gray-800);
        }

        .file-preview-content {
            height: 300px;
            overflow: hidden;
        }

        .file-preview-content iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0;
            }

            .card-body {
                padding: 1.5rem;
            }

            .upload-zone {
                padding: 1.5rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .status-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .status-card {
                padding: 1.25rem;
            }
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a
                                            href="javascript: void(0);">{{$page_info['page_title']}}</a></li>
                                    <li class="breadcrumb-item active">{{$page_info['page_name']}}</li>
                                </ol>
                            </div>
                            <h4 class="page-title">{{$page_info['page_name']}}</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title -->
                <div class="row  d-flex justify-content-center">
                   <div class="col-6">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Upload Zone -->

                                <div id="uploadZone" class="upload-zone">
                                    <input type="file" id="fileInput" accept=".pdf" style="display: none;">
                                    <svg class="upload-icon" id="uploadIcon" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <h3>Choose PDF File</h3>
                                    <p>or drag and drop bank statement here</p>

                                    <div id="fileInfo" class="file-info">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                            <div>
                                                <div style="font-weight: 600; color: var(--gray-800);" id="fileName"></div>
                                                <div style="font-size: 0.875rem; color: var(--gray-500);" id="fileSize">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Preview -->
                                <div id="filePreview" class="file-preview">
                                    <div class="file-preview-header">
                                        <h3 style="font-weight: 600; color: var(--gray-700);">Document Preview</h3>
                                        <button id="togglePreview" class="file-preview-toggle">
                                            <svg id="previewIcon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            <span id="previewText">Show Preview</span>
                                        </button>
                                    </div>
                                    <div id="previewContent" class="file-preview-content" style="display: none;">
                                        <iframe id="pdfPreview" title="PDF Preview"></iframe>
                                    </div>
                                </div>

                                <br>
                                <br>

                                <!-- Password Input -->
                                <div class="form-group">
                                    <label for="password" class="form-label">PDF Password (Optional)</label>
                                    <input type="password" id="password" class="form-control"
                                        placeholder="Enter password if PDF is encrypted">
                                </div>

                                <!-- Consent Checkbox -->
                                <div class="checkbox-container" style="display: none;">
                                    <input type="checkbox" id="consent" checked>
                                    <label for="consent">
                                        I hereby declare my consent agreement for fetching my information via ZOOP API and
                                        agree to the terms of service.
                                    </label>
                                </div>


                                <!-- Submit Button -->
                                <button id="submitBtn" class="btn btn-primary btn-block">
                                    <span id="submitSpinner" class="spinner" style="display: none;"></span>
                                    <span id="submitText">Start Analysis</span>
                                </button>

                                <!-- Progress Bar -->
                                <div id="progressContainer" class="progress-container">
                                    <div class="progress-info">
                                        <span>Overall Progress</span>
                                        <span id="progressPercent">0%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div id="progressFill" class="progress-fill"></div>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div id="errorMessage" class="error-message"></div>

                                <!-- COMPLETELY REDESIGNED STATUS SECTION -->
                                <div id="statusSection" class="status-section">
                                    <div class="status-header">
                                        <h3 class="status-title">Processing Uploaded Bank Statement</h3>
                                        <p class="status-subtitle">Please wait while we analyze your financial data</p>
                                    </div>

                                    <div class="status-grid">
                                        <div id="uploadCard" class="status-card pending">
                                            <div class="status-icon-container">
                                                <div class="status-icon" id="uploadIcon">
                                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                        <polyline points="17 8 12 3 7 8"></polyline>
                                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="status-content">
                                                <h3>File Upload</h3>
                                                <p>Securely uploading bank statement to our servers for processing</p>
                                                <div class="status-details">
                                                    Establishing encrypted connection and transferring file data...
                                                </div>
                                            </div>
                                        </div>

                                        <div id="analysisCard" class="status-card pending">
                                            <div class="status-icon-container">
                                                <div class="status-icon" id="analysisIcon">
                                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path d="M9 19c-5 0-8-3-8-8s3-8 8-8 8 3 8 8-3 8-8 8z"></path>
                                                        <path d="M17 17l4 4"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="status-content">
                                                <h3>Processing Analysis</h3>
                                                <p>This may take a while....</p>
                                                <div class="status-details">
                                                    Extracting transaction data, categorizing expenses, analyzing patterns
                                                    <span class="processing-dots">
                                                        <span class="processing-dot"></span>
                                                        <span class="processing-dot"></span>
                                                        <span class="processing-dot"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="reportCard" class="status-card pending">
                                            <div class="status-icon-container">
                                                <div class="status-icon" id="reportIcon">
                                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path
                                                            d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                        </path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="status-content">
                                                <h3>Report Generation</h3>
                                                <p>Creating comprehensive Excel report with detailed financial analysis</p>
                                                <div class="status-details">
                                                    Compiling insights into structured format and generating downloadable
                                                    report...
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="overall-progress">
                                        <h4>Overall Progress</h4>
                                        <div class="circular-progress">
                                            <svg>
                                                <defs>
                                                    <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                        <stop offset="0%" style="stop-color:#6366f1" />
                                                        <stop offset="100%" style="stop-color:#06b6d4" />
                                                    </linearGradient>
                                                </defs>
                                                <circle class="bg-circle" cx="40" cy="40" r="36"></circle>
                                                <circle class="progress-circle" cx="40" cy="40" r="36" id="progressCircle">
                                                </circle>
                                            </svg>
                                            <div class="progress-percentage" id="circularProgress">0%</div>
                                        </div>
                                        <p style="font-size: 0.875rem; color: var(--gray-500); margin: 0;">Processing
                                            financial data...</p>
                                    </div>
                                </div>

                                <!-- Download Section -->
                                <div id="downloadSection" class="download-section">
                                    <h3>Analysis Complete!</h3>
                                    <p>Uploaded bank statement has been successfully analyzed. Download the comprehensive
                                        financial report below.</p>
                                    <button id="downloadBtn" class="btn btn-download">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" style="margin-right: 8px;">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        Download Excel Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                   </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
@endsection

    @section('custom-js')

        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const API_CONFIG = {
                SUBMIT_URL: '{{ route("bsa.statement") }}',
                FETCH_URL: '{{ route("bsa.get-results") }}'
            };

            // DOM Elements
            const fileInput = document.getElementById('fileInput');
            const uploadZone = document.getElementById('uploadZone');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const passwordInput = document.getElementById('password');
            const consentCheckbox = document.getElementById('consent');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            const progressContainer = document.getElementById('progressContainer');
            const progressFill = document.getElementById('progressFill');
            const progressPercent = document.getElementById('progressPercent');
            const errorMessage = document.getElementById('errorMessage');
            const statusSection = document.getElementById('statusSection');
            const uploadCard = document.getElementById('uploadCard');
            const analysisCard = document.getElementById('analysisCard');
            const reportCard = document.getElementById('reportCard');
            const downloadSection = document.getElementById('downloadSection');
            const downloadBtn = document.getElementById('downloadBtn');
            const filePreview = document.getElementById('filePreview');
            const togglePreview = document.getElementById('togglePreview');
            const previewContent = document.getElementById('previewContent');
            const pdfPreview = document.getElementById('pdfPreview');
            const previewText = document.getElementById('previewText');
            const previewIcon = document.getElementById('previewIcon');
            const progressCircle = document.getElementById('progressCircle');
            const circularProgress = document.getElementById('circularProgress');

            // State
            let selectedFile = null;
            let orderId = null;
            let excelReportUrl = null;
            let previewUrl = null;
            let isPreviewVisible = false;

            // Event Listeners
            uploadZone.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', handleFileSelect);
            uploadZone.addEventListener('dragover', handleDragOver);
            uploadZone.addEventListener('dragleave', handleDragLeave);
            uploadZone.addEventListener('drop', handleFileDrop);
            submitBtn.addEventListener('click', submitAnalysis);
            downloadBtn.addEventListener('click', downloadReport);
            togglePreview.addEventListener('click', togglePdfPreview);

            // File Handling Functions
            function handleFileSelect(event) {
                const file = event.target.files[0];
                if (file && file.type === 'application/pdf') {
                    selectedFile = file;
                    displayFileInfo(file);
                    setupFilePreview(file);
                    uploadZone.classList.add('file-selected');
                    clearError();
                } else if (file) {
                    showError('Please select a valid PDF file');
                }
            }

            function handleDragOver(event) {
                event.preventDefault();
                uploadZone.classList.add('dragover');
            }

            function handleDragLeave(event) {
                event.preventDefault();
                uploadZone.classList.remove('dragover');
            }

            function handleFileDrop(event) {
                event.preventDefault();
                uploadZone.classList.remove('dragover');

                const file = event.dataTransfer.files[0];
                if (file && file.type === 'application/pdf') {
                    selectedFile = file;
                    fileInput.files = event.dataTransfer.files;
                    displayFileInfo(file);
                    setupFilePreview(file);
                    uploadZone.classList.add('file-selected');
                    clearError();
                } else if (file) {
                    showError('Please select a valid PDF file');
                }
            }

            function displayFileInfo(file) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.classList.add('show');
            }

            function setupFilePreview(file) {
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }
                previewUrl = URL.createObjectURL(file);
                pdfPreview.src = previewUrl;
                filePreview.classList.add('show');
            }

            function togglePdfPreview() {
                isPreviewVisible = !isPreviewVisible;
                if (isPreviewVisible) {
                    previewContent.style.display = 'block';
                    previewText.textContent = 'Hide Preview';
                    previewIcon.innerHTML = `
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                                <line x1="1" y1="1" x2="23" y2="23"></line>
                                            `;
                } else {
                    previewContent.style.display = 'none';
                    previewText.textContent = 'Show Preview';
                    previewIcon.innerHTML = `
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            `;
                }
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Analysis Functions
            async function submitAnalysis() {
                if (!selectedFile) {
                    showError('Please select a PDF file');
                    return;
                }

                if (!consentCheckbox.checked) {
                    showError('Please provide consent to proceed');
                    return;
                }

                try {
                    setLoading(true);
                    clearError();
                    showProgress();
                    updateProgress(10);

                    // Show status section and start upload
                    showStatusSection();
                    updateStatusCard('upload', 'processing');

                    const formData = new FormData();
                    formData.append('file', selectedFile);
                    formData.append('password', passwordInput.value || '');
                    formData.append('consent', 'Y');
                    formData.append('consent_text', 'I hereby declare my consent agreement for fetching my information via ZOOP API');

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const submitResponse = await fetch(API_CONFIG.SUBMIT_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                            // Do NOT set 'Content-Type' here; fetch will handle it with FormData
                        },
                        body: formData
                    });

                    if (!submitResponse.ok) {
                        throw new Error(`HTTP error! status: ${submitResponse.status}`);
                    }

                    const submitData = await submitResponse.json();

                    if (!submitData.success) {
                        throw new Error(submitData.message || 'Failed to submit bank statement');
                    }

                    orderId = submitData.orderId;
                    updateStatusCard('upload', 'completed');
                    updateProgress(30);

                    // Start analysis
                    updateStatusCard('analysis', 'processing');
                    await pollForResults(orderId);

                } catch (error) {
                    console.error('Error:', error);
                    showError(error.message || 'An error occurred while processing your request');
                    setLoading(false);
                    hideProgress();
                    resetStatusCards();
                }
            }

            async function pollForResults(orderIdParam) {
                const maxAttempts = 60;
                let attempts = 0;

                const pollInterval = setInterval(async () => {
                    attempts++;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const response = await fetch(`${API_CONFIG.FETCH_URL}?orderId=${orderIdParam}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken // Optional for GET, but safe for future-proofing
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.success && data.excelReportUrl) {
                            clearInterval(pollInterval);
                            excelReportUrl = data.excelReportUrl;

                            updateStatusCard('analysis', 'completed');
                            updateStatusCard('report', 'processing');

                            setTimeout(() => {
                                updateStatusCard('report', 'completed');
                                updateProgress(100);
                                setLoading(false);
                                showDownloadSection();
                            }, 1500);

                        } else if (attempts >= maxAttempts) {
                            clearInterval(pollInterval);
                            throw new Error('Processing timeout. Please try again later.');
                        }

                        const progressPercent = 30 + (attempts / maxAttempts) * 60;
                        updateProgress(Math.min(progressPercent, 90));

                    } catch (error) {
                        clearInterval(pollInterval);
                        throw error;
                    }
                }, 5000);
            }

            function downloadReport() {
                if (excelReportUrl) {
                    const link = document.createElement('a');
                    link.href = excelReportUrl;
                    link.download = 'bank_statement_analysis.xlsx';
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }

            // Enhanced UI Update Functions
            function updateStatusCard(step, status) {
                const cardMap = {
                    'upload': uploadCard,
                    'analysis': analysisCard,
                    'report': reportCard
                };

                const card = cardMap[step];
                if (!card) return;

                // Remove all status classes
                card.classList.remove('pending', 'processing', 'completed');
                card.classList.add(status);

                // Update icon based on status
                const iconElement = card.querySelector('.status-icon');
                if (status === 'completed') {
                    iconElement.innerHTML = `
                                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                    <polyline points="20,6 9,17 4,12"></polyline>
                                                </svg>
                                            `;
                }
            }

            function resetStatusCards() {
                [uploadCard, analysisCard, reportCard].forEach(card => {
                    card.classList.remove('processing', 'completed');
                    card.classList.add('pending');
                });
            }

            function showStatusSection() {
                statusSection.classList.add('show');

                // Animate cards with staggered delay
                setTimeout(() => uploadCard.classList.add('animate'), 200);
                setTimeout(() => analysisCard.classList.add('animate'), 400);
                setTimeout(() => reportCard.classList.add('animate'), 600);
            }

            function showDownloadSection() {
                // Hide the overall progress section
                const overallProgress = document.querySelector('.overall-progress');
                if (overallProgress) {
                    overallProgress.style.display = 'none';
                }

                // Hide the status grid (the processing cards)
                const statusGrid = document.querySelector('.status-grid');
                if (statusGrid) {
                    statusGrid.style.display = 'none';
                }

                // Hide the status header
                const statusHeader = document.querySelector('.status-header');
                if (statusHeader) {
                    statusHeader.style.display = 'none';
                }

                // Hide the progress container (linear progress bar)
                if (progressContainer) {
                    progressContainer.style.display = 'none';
                }

                // Show the download section with animation
                downloadSection.classList.add('show');

                // Optional: Add a success message to the status section
                statusSection.innerHTML = ` `;
            }

            function setLoading(isLoading) {
                submitBtn.disabled = isLoading;
                if (isLoading) {
                    submitSpinner.style.display = 'inline-block';
                    submitText.textContent = 'Processing...';
                } else {
                    submitSpinner.style.display = 'none';
                    submitText.textContent = 'Start Analysis';
                }
            }

            function showProgress() {
                progressContainer.classList.add('show');
            }

            function hideProgress() {
                progressContainer.classList.remove('show');
            }

            function updateProgress(percent) {
                progressFill.style.width = percent + '%';
                progressPercent.textContent = Math.round(percent) + '%';

                // Update circular progress
                const circumference = 2 * Math.PI * 36;
                const offset = circumference - (percent / 100) * circumference;
                progressCircle.style.strokeDashoffset = offset;
                circularProgress.textContent = Math.round(percent) + '%';
            }

            function showError(message) {
                errorMessage.textContent = message;
                errorMessage.classList.add('show');
            }

            function clearError() {
                errorMessage.classList.remove('show');
            }

            // Clean up function
            window.addEventListener('beforeunload', () => {
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }
            });
        </script>
    @endsection