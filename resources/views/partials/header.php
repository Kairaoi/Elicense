<!DOCTYPE html>

<head>
    
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(to bottom, #e0f7fa, #80deea);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        header {
            background: linear-gradient(135deg, #00796b, #004d40);
            color: #fff;
            text-align: center;
            padding: 40px 20px;
            border-bottom: 8px solid #004d40;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        header img {
            max-height: 100px;
            margin-bottom: 15px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .header-decorative {
            background: linear-gradient(90deg, #00796b, #004d40);
            height: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }

        /* Footer Styles Applied to Header */
        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(to right, #ffd700, #ff9900);
        }

        .footer-heading {
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: #ffd700;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: #ffd700;
            color: #1a3c6d;
            transform: translateY(-3px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                text-align: center;
            }

            .footer-heading::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .social-icons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <img src="http://localhost/fisherylicense/public/images/logos.png" alt="Official Seal" class="logo">
            
        </div>
    </header>

    

</body>
</html>
