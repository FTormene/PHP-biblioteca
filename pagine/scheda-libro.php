<!-- 
    Questa pagina mostra la scheda di un libro, con tutte le informazioni che ho nel DB su quel libro,
    è una cosa che nelle verifiche non chiedo, ma è interessante per mostrare come prendere un parametro 
    dalla URL (cod_libro) e usarlo per fare una query al DB e mostrare le informazioni del libro corrispondente, 
    e anche per mostrare come posso scrivere la descrizione del libro in un file markdown a parte e caricarla da lì, 
    così da non dover scrivere tutto il testo della descrizione direttamente nel campo del DB, che sarebbe scomodo, 
    soprattutto se la descrizione è lunga e ha anche formattazione (grassetto, elenchi puntati ecc), che con il markdown 
    è molto più semplice da gestire.
-->

<?php
    // controllo se la pagina è stata chiamata con il parametro cod_libro, 
    // altrimenti non posso sapere quale libro mostrare, quindi mostro un messaggio 
    // di errore e interrompo l'esecuzione del codice
    if (!isset($_GET["cod_libro"])) {
        die("Errore! manca un parametro essenziale per il caricamento del libro!");
    } 
    else {
        // se il parametro è presente, lo salvo in una variabile e faccio una query al DB per prendere 
        // tutte le informazioni del libro corrispondente a quel cod_libro
        $cod_libro = $_GET["cod_libro"];
        require("../data/connessione_db.php");
        $sql = "SELECT libri.cod_libro, libri.titolo, libri.copertina, libri.file_descrizione_md, libri.descrizione_txt, autori.nome, autori.cognome 
                FROM utenti JOIN libri ON utenti.username = libri.username_utente 
                            JOIN autori ON libri.cod_autore = autori.cod_autore  
                WHERE cod_libro=$cod_libro"; // qui non vanno le ' ' intorno a $cod_libro perchè è un numero
        $ris = $conn->query($sql) or die("<p>Query fallita!</p>");
        if ($ris->num_rows == 0) {
            // se il codice del libro non corrisponde a nessun libro nel DB, mostro un messaggio di errore 
            // e interrompo l'esecuzione del codice
            die ("Libro non trovato!");
        } 
        else {
            // salvo tutti i dati in variabili per poterli usare nel codice HTML più comodamente
            $riga = $ris->fetch_assoc();
            $titolo = $riga['titolo'];
            $file_descrizione_md = $riga['file_descrizione_md'];
            $descrizione_txt = $riga['descrizione_txt'];
            $nome = $riga["nome"];
            $cognome = $riga["cognome"];
            $copertina = $riga["copertina"];
        }
    }
?>


<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css" integrity="sha512-NhSC1YmyruXifcj/KFRWoC561YpHpc5Jtzgvbuzx5VozKpWvQ+4nXhPdFgmx8xqexRcpAglTj9sIBWINXa8x5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Biblioteca - Scheda Libro</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
	<?php require("nav.php");?>
	<div class="contenuto">
        
		<h1 style="text-align: center; margin-top: 0px"><?php echo $titolo?></h1>
        <div class="copertina-fr">
            <?php
                echo "<img src='../immagini/$copertina' alt='$copertina'>";
            ?>
        </div>
        <div class="descrizione"> <!-- div che contiene la descrizone del libro -->
            <?php

                // in questo esempio completo mostro tre modi diversi per scrivere la descrizione di un libro:

                // 1. testo semplice diviso in paragrafi con dei semplici a capo; l'unica cosa da fare è creare
                //    i paragrafi con la funzione explode() e poi stamparli con un ciclo foreach.
                //    Questo è il metodo più semplice che lascio come esempio principale perchè non troppo complicato
                //    e chiedibile in verifica, ma è un metodo che non permette di avere formattazione del testo 
                //    (grassetto, elenchi puntati ecc):

                $paragrafi = explode("\n", $descrizione_txt);
                foreach ($paragrafi as $paragrafo) {
                    echo "<p>$paragrafo</p>";
                }
                // echo "$descrizione_txt"; // solo debugging per confrontare il risultato con il testo grezzo, senza i paragrafi creati con explode() e senza i tag <p>
                

                // 2. testo scritto in markdown, ma direttamente nel campo descrizione_txt del DB, in questo caso basta
                //    usare la libreria parsedown.php (non scritta da me ma reperibile online) per convertire il testo 
                //    markdown in HTML, così da poterlo mostrare formattato nella pagina, invece di mostrare il testo 
                //    con i simboli del markdown (es. # per i titoli, * per il grassetto ecc):
                
                // include 'parsedown.php';
                // $Parsedown = new Parsedown();
                // echo $Parsedown->text($descrizione_txt);


                // 3. testo scritto in markdown, ma in un file a parte, il cui nome è salvato nel campo file_descrizione_md del DB.
                //    parsedown.php è una libreria, non scritta da me, che permette di convertire 
                //    un testo scritto in markdown in HTML, così da poterlo mostrare formattato nella pagina, 
                //    invece di mostrare il testo con i simboli del markdown (es. # per i titoli, * per il grassetto ecc)
                
                // $myfile = fopen("../schede/ilsignoredeglianelli.md", "r") or die("Unable to open file!");
                // $testo = fread($myfile,filesize("../schede/ilsignoredeglianelli.md"));
                // fclose($myfile);

                // include 'parsedown.php';
                // $Parsedown = new Parsedown();
                // echo $Parsedown->text($testo);
                

                // nel sito potrei mettere i punti 2 e 3 insieme controllando se nel DB ho il nome del file markdown, 
                // se ce l'ho allora carico la descrizione da quel file, altrimenti la prendo dal campo descrizione_txt del DB:

                // if ($file_descrizione_md) {
                //     $myfile = fopen("../schede/ilsignoredeglianelli.md", "r") or die("Unable to open file!");
                //     $testo = fread($myfile,filesize("../schede/ilsignoredeglianelli.md"));
                //     fclose($myfile);

                //     include 'parsedown.php';
                //     $Parsedown = new Parsedown();
                //     echo $Parsedown->text($testo);

                // } else {
                //     include 'parsedown.php';
                //     $Parsedown = new Parsedown();
                //     echo $Parsedown->text($descrizione_txt);
                // }

            ?>
        </div>
		
	</div>
	<?php 
		require('footer.php');
	?>	
	
</body>
</html>
<?php
	$conn->close();
?>