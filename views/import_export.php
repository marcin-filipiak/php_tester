<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Wyświetlanie Strony</title>
</head>
<body>

<?php include('views/menu.php'); ?>

<div class="editor-container">
    <h1>Import / Eksport pytań i odpowiedzi</h1>

    <form method="GET" action="">
        <input type="hidden" name="action" value="importexport">
        <select name="test_id" onchange="this.form.submit()">
            <option value="">Wybierz test</option>
            <?php foreach ($tests as $test): ?>
                <option value="<?= $test['id'] ?>" <?= $testId == $test['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($test['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <hr>

    <?php if ($testId): ?>
    <h2>Eksport</h2>
    <form method="POST" action="index.php?action=importexport&op=export">
        <input type="hidden" name="test_id" value="<?= $testId ?>">
        <button type="submit">Eksportuj do pliku JSON</button>
    </form>

    <h2>Import JSON</h2>
    <form method="POST" action="index.php?action=importexport&op=import" enctype="multipart/form-data">
        <input type="hidden" name="test_id" value="<?= $testId ?>">
        <label>Wklej JSON:</label><br>
        <textarea name="json_text" rows="10" cols="80"></textarea><br><br>
        <label>Lub wybierz plik:</label>
        <input type="file" name="json_file"><br><br>
        <button type="submit">Importuj dane</button>
    </form>
    <?php endif; ?>
    
   <hr> 
    
    <h1>Generator testu JSON AI</h1>

    <form id="testForm" onsubmit="generatePrompt(); return false;">
        <label>
            Tematyka testu:
            <input type="text" id="topic" required>
        </label>

        <label>
            Liczba pytań:
            <input type="number" id="questionCount" min="1" required>
        </label>

        <label>
            Liczba odpowiedzi na pytanie (dla pytań zamkniętych):
            <input type="number" id="answerCount" min="2" required>
        </label>

        <button type="submit">Generuj prompt</button>
        <button type="button" onclick="openInChatGPT()">Otwórz w ChatGPT</button>

        <label>
            Prompt:
            <textarea id="prompt" readonly></textarea>
        </label>
    </form>
    
        <script>
        function generatePrompt() {
            const topic = document.getElementById("topic").value.trim();
            const questionCount = parseInt(document.getElementById("questionCount").value);
            const answerCount = parseInt(document.getElementById("answerCount").value);
            const textarea = document.getElementById("prompt");

            let text = `Oto JSON, który jest wzorcem dla testu pytań i zawiera punktację odpowiedzi. Błędne mają punktację 0 a poprawne 1. Można też tworzyć pytania otwarte w których użytkownik ma podać odpowiedź. W miejscu odpowiedzi należy wstawić {?} i w takim pytaniu dajemy tylko jedną odpowiedź prawidłową o punktacji 1. \n\nPrzykład:\n[\n    {\n        "content": "5+5={?}",\n        "answers": [\n            {\n                "content": "10",\n                "points": "1"\n            }\n        ]\n    },\n    {\n        "content": "3-1 to",\n        "answers": [\n            {\n                "content": "10",\n                "points": "0"\n            },\n            {\n                "content": "2",\n                "points": "1"\n            }\n        ]\n    }\n]\n\n`;

            text += `Na tej podstawie zrób test dla ucznia w którym sprawdzimy wiedzę z zakresu: ${topic}\n\n`;
            text += `Test ma składać się z ${questionCount} pytań. `;
            text += `W przypadku pytań zamkniętych przynajmniej ${answerCount} odpowiedzi i 1 prawidłowa.`;

            textarea.value = text;
        }
        
        function openInChatGPT() {
            generatePrompt();
            const prompt = document.getElementById("prompt").value;
            const encoded = encodeURIComponent(prompt);
            const url = `https://chat.openai.com/?model=gpt-4&prompt=${encoded}`;
            window.open(url, '_blank');
        }
    </script>
    
</div>

</body>
</html>

