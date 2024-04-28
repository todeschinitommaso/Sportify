<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aggiungi Esercizio</title>
<style>
 body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        margin: 0;
        padding: 20px;
    }
    .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
    }
    .back-button button {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        background-color: #4CAF50;
        color: white;
        transition: background-color 0.3s;
    }
    .back-button button:hover {
        background-color: #45a049;
    }
    .form-container {
        margin-top: 50px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    .form-container label {
        display: block;
        margin-bottom: 10px;
    }
    .form-container input[type="text"],
    .form-container select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .form-container input[type="checkbox"] {
        margin-right: 10px;
    }
    .form-container input[type="submit"] {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        background-color: #4CAF50;
        color: white;
        transition: background-color 0.3s;
    }
    .form-container input[type="submit"]:hover {
        background-color: #45a049;
    }
</style>
</head>
<body>

<div class="back-button">
    <button onclick="window.location.href = 'gestione.php';">INDIETRO</button>
</div>

<div class="form-container">
<h1>AGGIUNGI ESERCIZIO</h1>
    <form method="post">
        <label for="nome_esercizio">Nome dell'esercizio:</label>
        <input type="text" name="nome_esercizio" id="nome_esercizio" required><br>
        <label for="serie">Serie:</label>
        <input type="text" name="serie" id="serie" required><br>
        <label for="reps">Ripetizioni:</label>
        <input type="text" name="reps" id="reps" required><br>
        <label for="pausa">Pausa:</label>
        <input type="text" name="pausa" id="pausa" required><br>
        <label for="peso">Peso:</label>
        <input type="text" name="peso" id="peso" required><br>
        <label for="intensita">Intensità:</label>
        <input type="text" name="intensita" id="intensita" required><br>
        <label for="muscolo">Muscolo:</label>
        <select name="muscolo" id="muscolo">
            <?php
                // Connessione al database
                $servername = "localhost";
                $username = "ceo";
                $password = "1234";
                $dbname = "tracker";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connessione fallita: " . $conn->connect_error);
                }

                // Query per ottenere i muscoli disponibili
                $sql = "SELECT id, nome FROM muscoli";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='".$row["id"]."'>".$row["nome"]."</option>";
                    }
                } else {
                    echo "<option value=''>Nessun muscolo trovato</option>";
                }

                $conn->close();
            ?>
        </select><br>
        <label for="altro">Altro:</label>
        <input type="text" name="altro" id="altro"><br>
        <input type="submit" name="submit" value="Aggiungi Esercizio">
    </form>
</div>

<?php
if (isset($_POST['submit'])) {
    $nome_esercizio = $_POST['nome_esercizio'];
    $serie = $_POST['serie'];
    $reps = $_POST['reps'];
    $pausa = $_POST['pausa'];
    $peso = $_POST['peso'];
    $intensita = $_POST['intensita'];
    $muscolo = $_POST['muscolo'];
    $altro = $_POST['altro'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Inserimento dell'esercizio nella tabella esercizi
    $sql_insert_esercizio = "INSERT INTO esercizi (nome, serie, reps, pausa, peso, intensita, id_muscolo, altro) VALUES ('$nome_esercizio', '$serie', '$reps', '$pausa', '$peso', '$intensita', '$muscolo', '$altro')";

    if ($conn->query($sql_insert_esercizio) === FALSE) {
        echo "Errore: " . $sql_insert_esercizio . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

</body>
</html>