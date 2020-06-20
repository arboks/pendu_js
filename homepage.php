<?php include 'header.php' ?>

<?php	if(isset($_POST['word']) === false)	{?>
		<script>var gameOn = false;</script>	
<?php	} else {?>
		<script>var gameOn = true;</script>	
<?php	}?>

<script>
	var lifeTxt = "Vies : ";
	var tryTxt = "Lettres essayées : ";
</script>

<div class="game">
	<div class="hidden" id="gameOff">	
		<i id="noCurrentGame">Il n'y a pas de partie en cours</i>
		<script>
			document.getElementById("noCurrentGame").removeAttribute("hidden");
		</script>

		<form>
			<table>	
				<tr>
					<td><input type="button" value="Nouveau mot" onclick="newWord()"></td>
					<td><input type="button" value="Mode d'emploi" onclick="displayRules()"></td>
				</tr>
			</table>
		</form>
	</div>
	
	<div class="hidden" id="gameOn">
		<div id="timer"></div>
		<div id="hiddenSecretWord"></div>
		<div id="life"></div>
		<div id="triedLetters"></div>
		<script></script>

		<form>
			<table>
				<tr>
					<td><label for="word">Mot :</label></td>
					<td><input type="text" id="word" name="word"></td>
					<td><input type="button" value="Soumettre" onclick="tryWord()"></td>
				</tr>
				<tr>
					<td><label for="letter">Lettre :</label></td>
					<td><input type="text" id="letter" name="letter" maxlength="1"></td>
					<td><input type="button" value="Soumettre" onclick="tryLetter()"></td>
				</tr>
				<tr>
					<td><input type="button" value="Nouveau mot" onclick="newWord()"></td>
					<td><input type="button" value="Mode d'emploi" onclick="displayRules()"></td>
				</tr>
			</table>
		</form>		
	</div>
</div>

<script>
	if(gameOn) {
		document.getElementById("gameOn").classList.remove('hidden');
	}
	else {
		document.getElementById("gameOff").classList.remove('hidden');
	}
	
	var alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";	
	var life = 10;
	var triedLetters = ""; //liste des lettres essayées
	
	var json = "";
	var secretWord = "";
	var hiddenSecretWord = "";
	var displayWord = "";
	
	// Affiche les secondes écoulées depuis le début de la partie
	var i=0;
	var timer;
	
	function increement() {
		
		i++;
		document.getElementById("timer").innerHTML = i + " secondes écoulées";
		
	}
	
	setInterval(function() {increement()}, 1000);
	// Fin Affichage secondes écoulées
	
	document.getElementById("life").innerHTML = lifeTxt + life.toString() + "/10";
	document.getElementById("triedLetters").innerHTML = tryTxt + triedLetters.toString();

	// Récupère toutes les positions du caractère charac dans la chaine str
	function getAllPos(str, charac){
		var indices = [];
		for(var i = 0; i < str.length; i++) {
			if (str[i] === charac) indices.push(i);
		}
		return indices;
	}
	
	function toDisplayFormat(str){
		return str.split('').join(' ');
	}

	function replaceAt(str, index, replacement) {
		return str.substr(0, index) + replacement + str.substr(index + replacement.length);
	}
	
	//test si la lettre écrite dans l'input est dans le mot secret
	function tryLetter(){
		var letter = document.getElementById("letter").value.toUpperCase();
		if(letter.length === 1 && alphabet.includes(letter)){
			triedLetters += " " + letter;
			document.getElementById("triedLetters").innerHTML = tryTxt + triedLetters.toString();
			var indexes = getAllPos(secretWord, letter);
			if(indexes.length === 0){
				wrong();
				return;
			}
			for(var i = 0; i < indexes.length; i++){
				hiddenSecretWord = replaceAt(hiddenSecretWord, indexes[i], letter);
			}	
			displayWord = toDisplayFormat(hiddenSecretWord);
			document.getElementById("hiddenSecretWord").innerHTML = displayWord;
			document.getElementById("letter").value = "";
		}
		else if(letter.length === 0){
			return;
		}
		else {
			document.getElementById("letter").value = "";
			alert('Choisissez 1 lettre de l\'alphabet.');
		}
		
		if(!hiddenSecretWord.includes("_"))
			win();
	}
	
	function tryWord(){
		var word = document.getElementById("word").value.toUpperCase();
		if(letter.length === 0){
			return;
		}
		else {
			if(word === secretWord)
				win();
			else
				wrong();
		}
		document.getElementById("word").value = "";		
	}

	//genere un nouveau mot
	function newWord(){	
		
		life = 10;
		// On remet les secondes à 0 en cas de victoire
		i=0;
		triedLetters = ""; //liste des lettres essayées		
		json = "";
		secretWord = "";
		hiddenSecretWord = "";
		displayWord = "";
		
		document.getElementById("life").innerHTML = lifeTxt + life.toString() + "/10";
		document.getElementById("triedLetters").innerHTML = tryTxt + triedLetters.toString();
		
		$.ajax({  
			type: "GET",
			dataType: "text",
			url: "getWord.php",
			success: function(data){
				// Here is the tip
				json = data;
			},
			complete: function() { 
				var obj = JSON.parse(json);
				console.log(obj.word);
				console.log(obj.secretWord);
				console.log(obj.displaySecretWord);
				
				secretWord = obj.word;
				hiddenSecretWord = obj.secretWord;
				displayWord = obj.displaySecretWord;
				document.getElementById("hiddenSecretWord").innerHTML = displayWord;
				document.getElementById("gameOn").classList.remove('hidden');
				document.getElementById("gameOff").classList.add('hidden');
            }
		});		
	}
	
	//affiche les règles du pendu
	function displayRules(){
		alert("Règles : \n \nVous disposez de 10 vies pour trouver le mot. Pour cela, écrivez une lettre dans la zone éponyme, puis vérifiez si elle est présente dans le mot en cliquant sur le bouton 'Soumettre' de la zone 'Lettre'. \n \nQuand vous pensez avoir trouvé le mot, écrivez le mot dans la zone éponyme, puis cliquez sur le bouton 'Soumettre' de la zone 'Mot'. \n \nVous perdrez une vie à chaque fois que vous choisissez une mauvaise lettre ou un mauvais mot. Essayez de ne pas vous faire pendre !");
	}
	
	function win(){
		alert("Bien joué ! Vous avez gagné en " + i + " secondes.");
		newWord();
	}
	
	function lose(){
		alert("Perdu !");
		newWord();
	}
	
	function wrong(){
		life--;
		if(life === 0){
			lose();
		}
		else {			
			document.getElementById("life").innerHTML = lifeTxt + life.toString() + "/10";
		}
	}
	
</script>
<?php include 'footer.php' ?>