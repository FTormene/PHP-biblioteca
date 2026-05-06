<?php
	session_start();
	//echo session_id();
    
	if(!isset($_SESSION['username'])){
		header('location: ../index.php');
	}
	
	$username = $_SESSION["username"];
	//echo $username;

	require('../data/connessione_db.php');

	if(isset($_POST['cod_libri'])){
        foreach($_POST['cod_libri'] as $cod_libro) {
            //echo $libro . '<br/>';
            $sql = "UPDATE libri
                    SET username_utente = '$username'
                    WHERE cod_libro = '$cod_libro'";
            $conn->query($sql) or die("<p>Query fallita!</p>");
        }
    }	
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css" integrity="sha512-NhSC1YmyruXifcj/KFRWoC561YpHpc5Jtzgvbuzx5VozKpWvQ+4nXhPdFgmx8xqexRcpAglTj9sIBWINXa8x5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<title>Biblioteca - Ritira</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
	<?php require("nav.php");?>
	<div class="contenuto">
		<h1 style="text-align: center; margin-top: 0px">Ricerca e ritiro dei libri</h1>
		<p>Cerca il libro che vuoi ritirare</p>

        <!-- form per la ricerca dei libri che mi interessa ritirare -->
		<form method="post" action="">
			<table id="tab_dati_personali">
				<tr>
					<td><label for="titolo_libro">Titolo:</label></td>
                    <td><input class="input_ricerca" type="text" name="titolo_libro" id="titolo_libro" value="<?php echo isset($_POST['titolo_libro']) ? $_POST['titolo_libro'] : ''; ?>"></td>
				</tr>
				<tr>
					<td>Nome autore:</td> <td><input class="input_ricerca" type="text" name="nome_autore" value="<?php echo isset($_POST['nome_autore']) ? $_POST['nome_autore'] : ''; ?>"></td>
				</tr>
				<tr>
					<td>Cognome autore:</td> <td><input class="input_ricerca" type="text" name="cognome_autore" value="<?php echo isset($_POST['cognome_autore']) ? $_POST['cognome_autore'] : ''; ?>"></td>
				</tr>
				<tr>
					<td style="text-align: center; padding-top: 10px" colspan="2"><input type="submit" value="Cerca"/></td>
				</tr>
			</table>
		</form>

		<p></p>

        <!-- form con l'elenco dei libri cercati e spuntabili -->
		<form method="post" action="">
            <?php
                if (isset($_POST["titolo_libro"]) and isset($_POST["nome_autore"]) and isset($_POST["cognome_autore"])) {
                    $titolo = $_POST["titolo_libro"];
                    $nome = $_POST["nome_autore"];
                    $cognome = $_POST["cognome_autore"];

                    $sql = "SELECT libri.cod_libro, libri.titolo, libri.copertina, autori.nome, autori.cognome, libri.username_utente 
                            FROM libri JOIN autori ON libri.cod_autore = autori.cod_autore  
                            WHERE titolo LIKE '%$titolo%'
                                AND nome LIKE '%$nome%'
                                AND cognome LIKE '%$cognome%'";
                    //echo $_POST["titolo_da_cercare"];
                    $ris = $conn->query($sql) or die("<p>Query fallita!</p>");
                    if ($ris->num_rows > 0) {
                        echo "<p>Scegli tra le soluzioni trovate i libri da ritirare.</p>";
                    
                        foreach($ris as $riga){
                            $cod_libro = $riga["cod_libro"];
                            $titolo = $riga["titolo"];
                            $copertina = $riga["copertina"];
                            $nome = $riga["nome"];
                            $cognome = $riga["cognome"];

                            echo <<<EOD
                                <div class="elenco_libri">
                                    <div class="card-libro">
                                        <div class="card-libro__img">
                                            <img src="../immagini/$copertina" alt="$copertina">
                                        </div>
                                        <div class="card-libro__testo">
                                            <div class="card-libro__testo__centrato">
                                                <p>Titolo: $titolo</p>
                                                <p>Autore: $nome $cognome</p>
                                                <p class="link-scheda"><a href="scheda-libro.php?cod_libro=$cod_libro">Scheda del libro</a></p>
                            EOD; 
                            if ($riga["username_utente"]){
                                echo "          <p>Disponibile: No</p>";
                            }
                            else {
                                echo "          <p>Disponibile: Sì</p>";
                                echo "          <p><input type='checkbox' name='cod_libri[]' value='$cod_libro'/> Spunta per prendere il libro</p>";
                            }
                            echo <<<EOD
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            EOD;
                        }
                        echo "<p style='text-align: center; padding-top: 10px'><input type='submit' value='Conferma'/></p>";
                    }
                    else {
                        echo "<p>Non ho trovato nessun libro che rispetti i valori indicati</p>";
                    }
                    
                }

            ?>
		</form>	

	</div>	
	<?php 
		include('footer.php')
	?>
</body>
</html>
<?php
	$conn->close();
?>