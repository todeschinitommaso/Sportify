<?php
session_start(); // Avvio della sessione

// Controlla se l'utente è autenticato, altrimenti reindirizza al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Logout se il parametro logout è impostato
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifica Allenamento</title>
<link rel="stylesheet" type="text/css" href="style.css">
<style>
    body {
        padding-top: 80px;
    }
</style>
</head>
<body>

<div class="back-button">
    <button onclick="window.location.href = 'gestione.php';">INDIETRO</button>
</div>

<div class="day-menu">
    <a href="modifica_allenamento.php?giorno=1">Lunedì</a>
    <a href="modifica_allenamento.php?giorno=2">Martedì</a>
    <a href="modifica_allenamento.php?giorno=3">Mercoledì</a>
    <a href="modifica_allenamento.php?giorno=4">Giovedì</a>
    <a href="modifica_allenamento.php?giorno=5">Venerdì</a>
    <a href="modifica_allenamento.php?giorno=6">Sabato</a>
    <a href="modifica_allenamento.php?giorno=7">Domenica</a>
</div>

<?php
include 'config.php';

// Funzione per aggiornare i dati nel database
function updateDato($conn, $id, $campo, $valore) {
    $sql = "UPDATE allenamento SET $campo = '$valore' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        return true; // Successo nell'aggiornamento
    } else {
        return false; // Errore nell'aggiornamento
    }
}

// Funzione per eliminare un esercizio
function eliminaEsercizio($conn, $id) {
    $sql = "DELETE FROM allenamento WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        return true; // Successo nell'eliminazione
    } else {
        return false; // Errore nell'eliminazione
    }
}

// Se viene inviata una richiesta di eliminazione di un esercizio
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['elimina_esercizio']) && isset($_GET['giorno'])) {
    $id = $_GET['elimina_esercizio'];

    // Elimina l'esercizio dal database
    if (eliminaEsercizio($conn, $id)) {
        // Successo
        header("Location: modifica_allenamento.php?giorno=" . $_GET['giorno']); // reindirizza per evitare l'invio di dati tramite refresh
        exit();
    } else {
        // Errore
        echo "<div style='text-align: center; color: red;'>Errore nell'eliminazione dell'esercizio.</div>";
    }
}

// Se viene inviata una richiesta di salvataggio
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['campo']) && isset($_POST['valore'])) {
    $id = $_POST['id'];
    $campo = $_POST['campo'];
    $valore = $_POST['valore'];

    // Aggiorna il dato nel database
    if (updateDato($conn, $id, $campo, $valore)) {
        // Successo
        echo "<div style='text-align: center; color: green;'>Dato aggiornato con successo!</div>";
    } else {
        // Errore
        echo "<div style='text-align: center; color: red;'>Errore nell'aggiornamento del dato.</div>";
    }
}

// Determina l'id del giorno corrispondente al giorno selezionato
$giorno_selezionato = isset($_GET['giorno']) ? $_GET['giorno'] : 1; // default a Lunedì se non specificato
$giorni_settimana = array(
    1 => 'Lunedì',
    2 => 'Martedì',
    3 => 'Mercoledì',
    4 => 'Giovedì',
    5 => 'Venerdì',
    6 => 'Sabato',
    7 => 'Domenica'
);
$giorno_corrente = $giorni_settimana[$giorno_selezionato];

// Query per ottenere i dati degli esercizi per il giorno selezionato
$sql = "SELECT a.id AS id_allenamento, 
               e.id AS id_esercizio, 
               e.nome AS nome_esercizio, 
               a.serie, 
               a.reps, 
               a.pausa, 
               a.peso, 
               a.intensita, 
               m.nome AS nome_muscolo, 
               a.altro
        FROM allenamento a
        INNER JOIN giorni g ON a.id_giorno = g.id
        INNER JOIN esercizi e ON a.id_esercizio = e.id
        INNER JOIN muscoli m ON e.id_muscolo = m.id
        WHERE g.id = $giorno_selezionato AND a.id_utente = $user_id";

$result = $conn->query($sql);

// Visualizzazione della tabella con l'allenamento per il giorno selezionato
echo "<h1>Allenamento del $giorno_corrente</h1>";
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Esercizio</th>";
    echo "<th style='width: 100px;'>Serie</th>"; // Accorcia la larghezza
    echo "<th style='width: 100px;'>Reps</th>"; // Accorcia la larghezza
    echo "<th style='width: 100px;'>Pausa</th>"; // Accorcia la larghezza
    echo "<th style='width: 100px;'>Peso</th>"; // Accorcia la larghezza
    echo "<th>Intensità</th>";
    echo "<th>Muscolo</th>";
    echo "<th>Altro</th>";
    echo "<th>Azioni</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["nome_esercizio"]."</td>";
        echo "<td><input type='text' class='editable-input' name='serie' value='".$row["serie"]."' onchange='salvaModifiche(".$row["id_allenamento"].", \"serie\", this.value)'></td>";
        echo "<td><input type='text' class='editable-input' name='reps' value='".$row["reps"]."' onchange='salvaModifiche(".$row["id_allenamento"].", \"reps\", this.value)'></td>";
        echo "<td><input type='text' class='editable-input' name='pausa' value='".$row["pausa"]."' onchange='salvaModifiche(".$row["id_allenamento"].", \"pausa\", this.value)'></td>";
        echo "<td><input type='text' class='editable-input' name='peso' value='".$row["peso"]."' onchange='salvaModifiche(".$row["id_allenamento"].", \"peso\", this.value)'></td>";
        echo "<td><input type='text' class='editable-input' name='intensita' value='".$row["intensita"]."' onchange='salvaModifiche(".$row["id_allenamento"].", \"intensita\", this.value)'></td>";
        echo "<td>".$row["nome_muscolo"]."</td>";
        echo "<td><input type='text' class='editable-input' name='altro' value='".$row["altro"]."' onchange='salvaModifiche(".$row["id_allenamento"].", \"altro\", this.value)'></td>";
        echo "<td><button class='delete-button' onclick='eliminaEsercizio(".$row["id_allenamento"].")'>Elimina</button></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>Nessun risultato trovato per il $giorno_corrente</p>";
}

$conn->close();
?>


<script>
function eliminaEsercizio(idAllenamento) {
    if (confirm("Sei sicuro di voler eliminare questo esercizio?")) {
        window.location.href = "modifica_allenamento.php?elimina_esercizio=" + idAllenamento + "&giorno=<?php echo $giorno_selezionato; ?>";
    }
}

function salvaModifiche(idAllenamento, campo, valore) {
    // Chiamata AJAX per salvare le modifiche
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
    };
    xhttp.open("POST", "modifica_allenamento.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + idAllenamento + "&campo=" + campo + "&valore=" + encodeURIComponent(valore));
}
</script>

</body>
</html>


<script>
function eliminaEsercizio(idAllenamento) {
    if (confirm("Sei sicuro di voler eliminare questo esercizio?")) {
        // Chiamata AJAX per eliminare l'esercizio
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Ricarica la pagina dopo l'eliminazione dell'esercizio
                location.reload();
            }
        };
        xhttp.open("GET", "modifica_allenamento.php?elimina_esercizio=" + idAllenamento + "&giorno=<?php echo $giorno_selezionato; ?>", true);
        xhttp.send();
    }
}

function salvaModifiche(idAllenamento, campo, valore) {
    // Chiamata AJAX per salvare le modifiche
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
    };
    xhttp.open("POST", "modifica_allenamento.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + idAllenamento + "&campo=" + campo + "&valore=" + encodeURIComponent(valore));
}
</script>

</body>
</html>