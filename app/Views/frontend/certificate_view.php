<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - <?= esc($booking['listing_title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --cnd-pink: #FF68B4;
            --cnd-purple: #7C4DFF;
            --gold: #D4AF37;
        }
        body { 
            margin: 0; padding: 0; 
            background: #f0f2f5; 
            font-family: 'Montserrat', sans-serif;
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh;
        }
        .outer-border {
            width: 800px; height: 600px;
            padding: 20px;
            background: #fff;
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }
        .inner-border {
            width: 100%; height: 100%;
            border: 10px double var(--gold);
            box-sizing: border-box;
            display: flex; flex-direction: column; align-items: center;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .logo { width: 120px; margin-bottom: 20px; }
        .cert-title { font-size: 42px; font-weight: 800; color: var(--cnd-purple); text-transform: uppercase; letter-spacing: 5px; margin: 0; }
        .cert-subtitle { font-size: 18px; margin: 10px 0 30px; color: #555; letter-spacing: 2px; }
        .presented-to { font-size: 16px; font-weight: 400; font-style: italic; color: #777; margin-bottom: 10px; }
        .student-name { font-family: 'Dancing Script', cursive; font-size: 56px; color: var(--cnd-pink); margin-bottom: 20px; border-bottom: 2px solid #eee; padding: 0 40px; }
        .completion-text { font-size: 16px; line-height: 1.6; color: #444; max-width: 600px; }
        .course-title { font-weight: 700; color: #000; display: block; font-size: 20px; margin-top: 5px; }
        .footer { 
            width: 100%; 
            display: flex; justify-content: space-around; 
            margin-top: auto; 
            padding-bottom: 20px;
        }
        .sig-block { border-top: 1px solid #aaa; padding-top: 10px; width: 200px; }
        .sig-label { font-size: 12px; color: #888; text-transform: uppercase; }
        .sig-name { font-weight: 700; font-size: 14px; margin-top: 5px; }
        
        .badge-ribbon {
            position: absolute; bottom: 80px; left: 50%; transform: translateX(-50%);
            width: 80px; height: 80px;
        }

        .print-btn {
            position: fixed; top: 20px; right: 20px;
            background: var(--cnd-pink); color: #fff; border: none;
            padding: 12px 25px; border-radius: 30px; cursor: pointer;
            font-weight: 700; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        @media print {
            .print-btn { display: none; }
            body { background: #fff; padding: 0; }
            .outer-border { box-shadow: none; margin: 0; width: 100%; height: 100vh; padding: 0; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">Download / Print Certificate</button>

    <div class="outer-border">
        <div class="inner-border">
            <img src="<?= base_url('assets/frontend/img/logo.png') ?>" class="logo" alt="Logo">
            
            <h1 class="cert-title">Certificate</h1>
            <p class="cert-subtitle">OF COMPLETION</p>
            
            <p class="presented-to">This certificate is proudly presented to</p>
            <div class="student-name"><?= esc($booking['student_name']) ?></div>
            
            <p class="completion-text">
                For successfully completing the program
                <span class="course-title"><?= esc($booking['listing_title']) ?></span>
                on <?= date('d F, Y', strtotime($booking['completed_at'] ?: $booking['updated_at'])) ?>
            </p>

            <div class="footer">
                <div class="sig-block">
                    <div class="sig-name"><?= esc($booking['provider_name']) ?></div>
                    <div class="sig-label">Instructor / Provider</div>
                </div>
                <div class="sig-block">
                    <div class="sig-name">Anusha Devi</div>
                    <div class="sig-label">Director, Class Next Door</div>
                </div>
            </div>
            
            <div style="margin-top: 15px; font-size: 10px; color: #aaa;">Certificate ID: CND-<?= str_pad($booking['id'], 6, '0', STR_PAD_LEFT) ?></div>
        </div>
    </div>

    <script>
        // Auto prompt print after a short delay
        window.addEventListener('load', function() {
            setTimeout(function() {
                // window.print();
            }, 1000);
        });
    </script>
</body>
</html>
