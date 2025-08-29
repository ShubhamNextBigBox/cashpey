<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanction Letter Acceptance</title>
    <style>
        /* Main container styling */
        .sanction-container {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            max-width: 640px;
            margin: 2rem auto;
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            color: #1e293b;
            line-height: 1.6;
        }
        
        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .info-icon {
            color: #17a2b8;
            font-size: 48px;
            margin-bottom: 20px;
        }

        /* Header section */
        .sanction-header {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .sanction-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1e40af;
            margin: 0 0 0.5rem 0;
        }

        /* Content section */
        .sanction-content {
            margin: 2rem 0;
        }

        .sanction-greeting {
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .sanction-message {
            margin-bottom: 1.5rem;
        }

        /* Details section */
        .sanction-details {
            background: #f8fafc;
            padding: 1.25rem;
            border-radius: 6px;
            margin: 1.5rem 0;
        }

        .detail-row {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .detail-label {
            font-weight: 600;
            min-width: 150px;
        }

        /* Footer section */
        .sanction-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.95rem;
        }

        .contact-info {
            margin-top: 1rem;
        }

        .contact-method {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .contact-icon {
            margin-right: 0.5rem;
            color: #4f46e5;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .sanction-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="sanction-container">
        <div class="sanction-header">
            @if($alreadyApproved)
                <div class="info-icon">ℹ️</div>
                <h1 class="sanction-title">Pre Approval Letter - consent already shared</h1>
            @else
                <div class="success-icon">✓</div>
                <h1 class="sanction-title">Pre Approval Letter Accepted Successfully</h1>
            @endif
        </div>
        
        <div class="sanction-content">
            <p class="sanction-greeting">Dear Customer,</p>
            
            @if($alreadyApproved)
                <p class="sanction-message">We have already received your consent on your pre approval letter on {{ df($approvalDate) }}.</p>
            @else
                <p class="sanction-message">We're pleased to inform you that we have successfully received your pre approval acceptance.</p>
            @endif
            
            <div class="sanction-details">
                <div class="detail-row">
                    <div class="detail-label">Date of Pre-Approval: </div> <div>&nbsp; {{ df($approvalDate) }}</div>
                </div>
            </div>
        </div>
        
        <div class="sanction-footer">
            <p>Thank you for your consent, we are proceeding with further verification. you can connect at customer care number & email for your query.</p>
            <div class="contact-info">
                <div class="contact-method">
                    <span class="contact-icon">✉️</span>
                    <span>customerservice@cashpey.com</span>
                </div>
                <div class="contact-method">
                    <span class="contact-icon">📞</span>
                    <span>+91-7003270034</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>