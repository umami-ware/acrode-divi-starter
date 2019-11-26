<?php
class AcrodeDevelopmentMode
{
    private static function development_design()
    {
        global $acDevelopment;

        $endTime = mktime(0, 0, 0, $acDevelopment['releaseMonth'], $acDevelopment['releaseDay'], $acDevelopment['releaseYear']); //Stunde, Minute, Sekunde, Monat, Tag, Jahr; 
        //Aktuellezeit des microtimestamps nach PHP5, für PHP4 muss eine andere Form genutzt werden. 
        $timeNow = microtime(true);
        //Berechnet differenz von der Endzeit vom jetzigen Zeitpunkt aus. 
        $diffTime = $endTime - $timeNow;
        //Berechnung für Tage, Stunden, Minuten 
        $day = floor($diffTime / (24 * 3600));
        $diffTime = $diffTime % (24 * 3600);
        $hours = floor($diffTime / (60 * 60));
        $diffTime = $diffTime % (60 * 60);
        $mins = floor($diffTime / 60);
        $secs = $diffTime % 60;
        ?>
        <style>
            body {
                background: transparent !important;
                box-shadow: none !important;
                overflow: hidden;
            }

            h1 {
                font-size: 51px !important;
                line-height: 1.1;
                color: rgba(255, 255, 255, 0.75) !important;
                text-transform: uppercase;
                letter-spacing: 10px;
                text-align: center;
            }

            h2 {
                text-align: center;
                color: #fff !important;
                margin-bottom: 50px;
            }

            .ac-development-container {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: linear-gradient(-180deg, <?php echo $acDevelopment['acColorOne']; ?> 25%, <?php echo $acDevelopment['acColorTwo']; ?>) no-repeat !important;
            }

            .ac-countdown-container {
                display: flex;
                align-items: center;
                justify-content: center;
                <?php echo $acDevelopment['hideTimer'] ? 'display: none' : ''; ?>
            }

            .ac-countdown {
                display: flex;
                flex-direction: column;
                color: #fff;
                margin: 0 25px;
            }

            .ac-countdown>span:first-child {
                font-size: 85px;
            }

            .ac-unit {
                text-align: center;
                font-size: 17px;
                font-weight: bold;
                text-transform: uppercase;
                color: rgba(255, 255, 255, 0.85);
            }

            @media (max-width: 620px) {
                h1 {
                    font-size: 31px !important;
                }

                h2 {
                    font-size: 22px !important;
                }

                .ac-countdown {
                    margin: 0 10px;
                }

                .ac-countdown>span:first-child {
                    font-size: 11vw;
                }

                .ac-unit {
                    font-size: 11px;
                }
            }

            @media (max-height: 299px),
            (max-width: 299px) {
                body {
                    display: none !important;
                }

                html::after {
                    content: 'Leider ist die Bildschirmgröße zu klein. Bitte besuchen Sie die Seite auf einem Gerät mit einem größerem Bildschirm.';
                    display: block !important;
                    margin: auto;
                    font-size: 26px;
                    line-height: 26px;
                    color: #000;
                    background: #f1f1f1;
                    text-align: center;
                    padding: 0 10px;
                    word-break: break-word;
                }
            }
        </style>
        <div class="ac-development-container">
            <h1>COMING SOON!</h1>
            <h2><?php echo $acDevelopment['h2Text'] ?></h2>
            <div class="ac-countdown-container">
                <div class="ac-countdown">
                    <span id="ac-days"><?php echo max($day, 0); ?></span>
                    <span class="ac-unit">Tage</span>
                </div>
                <div class="ac-countdown">
                    <span id="ac-hours"><?php echo max($hours, 0); ?></span>
                    <span class="ac-unit">Stunden</span>
                </div>
                <div class="ac-countdown">
                    <span id="ac-minutes"><?php echo max($mins, 0); ?></span>
                    <span class="ac-unit">Minuten</span>
                </div>
                <div class="ac-countdown">
                    <span id="ac-seconds"><?php echo max($secs, 0); ?></span>
                    <span class="ac-unit">Sekunden</span>
                </div>
            </div>
        </div>
        <script>
            var countDownDate = new Date(<?php echo $endTime; ?> * 1000).getTime();
            elDays = document.getElementById("ac-days"),
                elHours = document.getElementById("ac-hours"),
                elMinutes = document.getElementById("ac-minutes"),
                elSeconds = document.getElementById("ac-seconds");
            var x = setInterval(function() {
                // Get today's date and time
                var now = new Date().getTime();
                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                elDays.innerHTML = Math.max(0, Math.floor(distance / (1000 * 60 * 60 * 24)));
                elHours.innerHTML = Math.max(0, Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
                elMinutes.innerHTML = Math.max(0, Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)));
                elSeconds.innerHTML = Math.max(0, Math.floor((distance % (1000 * 60)) / 1000));
            }, 1000);
        </script>
    <?php
        }
        public static function ac_link_script()
        {
            ?>
        <script>
            document.querySelectorAll('a').forEach(function(elem) {
                elem.addEventListener('click', function(e) {
                    var href = e.target.closest('a').href,
                        location = window.location;
                    if (location.href.split('#').shift() !== href.split('#').shift()) {
                        e.preventDefault();
                        var splitHash = href.split('#'),
                            getParams = location.search.substr(1),
                            newLink;
                        if (splitHash[0].indexOf('?') === -1) {
                            newLink = splitHash.shift() + '?' + getParams;
                        } else {
                            newLink = splitHash.shift() + '&' + getParams;
                        }
                        if (splitHash.length) {
                            newLink += '#' + splitHash.join('');
                        }
                        window.location.href = newLink;
                    }
                });
            });
        </script>
<?php
    }
    public static function development_mode()
    {
        global $acDevelopment;
        $showSite = $_GET['viewSiteKey'];
        if ($showSite !== $acDevelopment['viewSiteKey'] && (!current_user_can('edit_themes') || !is_user_logged_in())) {
            wp_die(AcrodeDevelopmentMode::development_design());
        } else {
            add_action('wp_footer', 'ac_link_script');
        }
    }

    public static function setup()
    {
        add_action('get_header', array('AcrodeDevelopmentMode', 'development_mode'));
    }
}
AcrodeDevelopmentMode::setup();
