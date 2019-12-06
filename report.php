<?php declare (strict_types=1);

    const USER_ID = 7631950;
    const MINIMUM_DATE = 1546300800;
    const PARAMETERS = array (':userid' => USER_ID, ':date' => MINIMUM_DATE);

    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:tautulli.sqlite');
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $preparePlayTime = $file_db->prepare ('
SELECT
	SUM(session_history_metadata.duration) AS miliseconds
FROM session_history, session_history_metadata
WHERE session_history_metadata.id = session_history.id AND
	session_history.user_id = :userid AND
	session_history.started > :date;'
    );
    $preparePlayTimeByArtist = $file_db->prepare ('
SELECT
	SUM(session_history_metadata.duration) AS miliseconds,
	session_history_metadata.grandparent_title,
	session_history_metadata.media_type
FROM session_history, session_history_metadata
WHERE session_history_metadata.id = session_history.id AND
	session_history.user_id = :userid AND
	session_history.started > :date
GROUP BY session_history_metadata.grandparent_title
ORDER BY miliseconds DESC;'
    );
    $preparePlayCountByArtist = $file_db->prepare ('
SELECT
	COUNT(*) AS plays,
	session_history_metadata.grandparent_title,
	session_history_metadata.media_type
FROM session_history, session_history_metadata
WHERE session_history_metadata.id = session_history.id AND
	session_history.user_id = :userid AND
	session_history.started > :date
GROUP BY session_history_metadata.grandparent_title
ORDER BY plays DESC;'
    );
    $preparePlayTimeByTrack = $file_db->prepare ('
SELECT
	SUM(session_history_metadata.duration) AS miliseconds,
	session_history_metadata.grandparent_title,
	session_history_metadata.title,
	session_history_metadata.media_type
FROM session_history, session_history_metadata
WHERE session_history_metadata.id = session_history.id AND
	session_history.user_id = :userid AND
	session_history.started > :date
GROUP BY session_history_metadata.grandparent_title, session_history_metadata.title
ORDER BY miliseconds DESC;'
    );
    $preparePlayCountByTrack = $file_db->prepare ('
SELECT
	COUNT(*) AS plays,
	session_history_metadata.grandparent_title,
	session_history_metadata.title,
	session_history_metadata.media_type
FROM session_history, session_history_metadata
WHERE session_history_metadata.id = session_history.id AND
	session_history.user_id = :userid AND
	session_history.started > :date
GROUP BY session_history_metadata.grandparent_title, session_history_metadata.title
ORDER BY plays DESC;'
    );
    $preparePlayCountByAlbum = $file_db->prepare ('
SELECT
	COUNT(*) AS plays,
	session_history_metadata.grandparent_title,
	session_history_metadata.parent_title,
	session_history_metadata.media_type
FROM session_history, session_history_metadata
WHERE session_history_metadata.id = session_history.id AND
	session_history.user_id = :userid AND
	session_history.started > :date
GROUP BY session_history_metadata.grandparent_title, session_history_metadata.parent_title
ORDER BY plays DESC;'
    );

    $preparePlayTime->execute (PARAMETERS);
    $preparePlayTimeByArtist->execute (PARAMETERS);
    $preparePlayCountByArtist->execute (PARAMETERS);
    $preparePlayTimeByTrack->execute (PARAMETERS);
    $preparePlayCountByTrack->execute (PARAMETERS);
    $preparePlayCountByAlbum->execute (PARAMETERS);

    function format_date_diff ($timestamp)
    {

        $start = new DateTime ("@0");
        $end = new DateTime ("@" .  ceil ($timestamp / 1000));

        return $start->diff ($end)->format ("%a days, %h hours, %i minutes, %s seconds");
    }

    echo "Total play time: " . format_date_diff ($preparePlayTime->fetch (PDO::FETCH_ASSOC) ['miliseconds']) . "\n";
    echo "------------------------------------\n";

    for ($i = 0; $i < 15; $i ++)
    {
        $entry = $preparePlayTimeByArtist->fetch (PDO::FETCH_ASSOC);

        echo "({$entry ['media_type']}s) {$entry ['grandparent_title']}: " . format_date_diff ($entry ['miliseconds']) . "\n";
    }

    echo "------------------------------------\n";

    for ($i = 0; $i < 15; $i ++)
    {
        $entry = $preparePlayCountByArtist->fetch (PDO::FETCH_ASSOC);

        echo "({$entry ['media_type']}s) {$entry ['grandparent_title']}: Played {$entry ['plays']} times\n";
    }

    echo "------------------------------------\n";

    for ($i = 0; $i < 15; $i ++)
    {
        $entry = $preparePlayTimeByTrack->fetch (PDO::FETCH_ASSOC);

        echo "({$entry ['media_type']}s) {$entry ['title']} - {$entry ['grandparent_title']}: " . format_date_diff ($entry ['miliseconds']) . "\n";
    }

    echo "------------------------------------\n";

    for ($i = 0; $i < 15; $i ++)
    {
        $entry = $preparePlayCountByTrack->fetch (PDO::FETCH_ASSOC);

        echo "({$entry ['media_type']}s) {$entry ['title']} - {$entry ['grandparent_title']}: Played {$entry ['plays']} times\n";
    }

    echo "------------------------------------\n";

    for ($i = 0; $i < 15; $i ++)
    {
        $entry = $preparePlayCountByAlbum->fetch (PDO::FETCH_ASSOC);

        echo "({$entry ['media_type']}s) {$entry ['parent_title']} - {$entry ['grandparent_title']}: Played {$entry ['plays']} times\n";
    }