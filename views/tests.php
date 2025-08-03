<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Wyświetlanie Strony</title>
</head>
<body>

<? include("views/menu.php"); ?>

    <div class="page-container">
	<h1>Testy</h1>
	
		<a href="index.php?action=tests&op=add">+ Dodaj nowy test</a>
	
	<table>
	<tr><th>Nazwa</th><th>Przedmiot</th><th>Akcje</th></tr>	
	<?php      
	foreach ($testList as $tests) {
	    echo '<tr>';
	    echo '<td>' . $tests['name'] . '</td>';
	    echo '<td>' . $tests['subiect_name'] . '</td>';
	    echo '<td>
			<a href="index.php?action=test_questions&testId=' . $tests['id'] . '">Pytania</a>
			<a href="index.php?action=tests&op=edit&testId='.$tests['id'].'">Edycja</a>
            <a href="index.php?action=tests&op=delete&testId='.$tests['id'].'" onclick="return confirm(\'Na pewno usunąć test z pytaniami, oceny i przypisania do klas?\')">Kasuj</a>
		  </td>';
	    echo '</tr>';
	}
	?>
	</table>
    </div>

</body>
</html>

