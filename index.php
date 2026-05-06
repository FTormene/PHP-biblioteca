<?php
    session_start();
    if(isset($_SESSION['username'])){
        header('location: pagine/home.php');
        exit();
    }

    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    } else {
        $username = '';
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Biblioteca-Login</title>
</head>
<body>
    <?php
        require("pagine/nav.php");
    ?>
    <div class="contenuto">
        <h1>Biblioteca Online</h1>
		<h2>Pagina di Login</h2>

        <form action="" method="post">
            <table class="tab_input">
                <tr>
                    <td><label for="username">Username: </label></td>
                    <td><input type="text" name="username" id="username" value = "<?php echo $username; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="password">Password: </label></td>
                    <td><input type="password" name="password" id="password" required></td>
                </tr>
            </table>
            <label for="utente">Utente</label>
            <input type="radio" name="tipo_utente" id="utente" value="utente" checked>

            <label for="bibliotecario">Bibliotecario</label>
            <input type="radio" name="tipo_utente" id="bibliotecario" value="bibliotecario">
            <br><br>
            <input type="submit" value="Accedi">
        </form>
        <?php
            // codice del login

            // controllo che la pagina sia stata chiamata col metodo post e siano settati username e password
            if (/*$_SERVER["REQUEST_METHOD"] == "POST" && */isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["tipo_utente"])  ) {
                $tipo_utente = $_POST["tipo_utente"];
                if ($tipo_utente == "utente") {
                    $tabella = "utenti";
                } else if ($tipo_utente == "bibliotecario") {
                    // $tabella = "bibliotecari";   // non esiste ancora la tabella dei bibliotecari, 
                                                    // quindi per ora non è possibile fare il login come bibliotecario
                    echo "<p>Per ora la sezione bibliotecari non è disponibile.</p>";
                    exit();
                } else {
                    // non dovrebbe essere possibile arrivare qui perchè i radio button sono esclusivi
                    // e ho messo checked su uno dei due, ma metto questo controllo ridondante per sicurezza
                    echo "<p>Seleziona un tipo di utente.</p>";
                    exit();
                }

                // salvo i valori di username e password in variabili per comodità
                $username = $_POST["username"];
                $password = $_POST["password"];

                // connessione al DB
                require_once("data/connessione_db.php");

                // creazione della query che prende l'utente con quelle credenziali
                $myquery = "SELECT username, password 
                            FROM $tabella 
                            WHERE username = '$username' AND password = '$password'";
                
                // esecuzione della query
                $ris = $conn->query($myquery);

                // verifico il risultato cioè se è presente un utente con quelle credenziali
                // potrei scrivere if ($ris->num_rows == 1) tanto username è chiave primaria 
                // e quindi non può esserci più di un utente con quello username
                if ($ris->num_rows > 0) { // potevo mettere == 1
                    // se è presente allora l'utente è autenticato
                    echo "<p>Login effettuato con successo!</p>"; // non lo vedrò mai perchè poi faccio un redirect, ma lo metto per completezza

                    // carico lo username in sessione per poterlo usare nelle altre pagine
                    session_start();
                    $_SESSION["username"] = $username; // salvo lo username in sessione

                    // reindirizzo alla pagina principale
                    header("location: pagine/home.php");
                    $conn->close();
                    exit(); // interrompo l'esecuzione del codice visto che abbandono la pagina con il redirect
                } else {
                    // altrimenti le credenziali sono errate
                    echo "<p>Username o password errati. Riprova.</p>";
                }
                $conn->close();
            }
        ?>

        
        <?php
            require('pagine/footer.php');
        ?>
            
    </div>
</body>
</html>

