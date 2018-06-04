<!DOCTYPE html>


<html lang="en">
	<head>
        <title>Display Movie Information</title>
		<meta charset="utf-8"/>				
	<!--	<meta name="viewport" content="width=device-width, initial-scale=1">	-->
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="https://jqueryui.com/resources/demos/style.css">
        <link href="bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" rel="stylesheet">

		<style>
		
			div.character-panel {
                width:100%;
			}
					
			img.photo {
				max-height: 200px;				
				max-width: 200px;
			}

            h1 {
                text-align: center;
                color: #DEF9D8;
                font-size: 4em;
                font-weight: bold;
                font-family: Helvetica;
                text-shadow: 0 1px 0 #ccc, 0 2px 0 #c9c9c9, 0 3px 0 #bbb, 0 4px 0 #b9b9b9, 0 5px 0 #aaa, 0 6px 1px rgba(0,0,0,.1), 0 0 5px rgba(0,0,0,.1), 0 1px 3px rgba(0,0,0,.3), 0 3px 5px rgba(0,0,0,.2), 0 5px 10px rgba(0,0,0,.25), 0 10px 10px rgba(0,0,0,.2), 0 20px 20px rgba(0,0,0,.15);
            }

		</style>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>				
		<script>
		
			$( function() {
			$( ".tags" ).autocomplete({
			  source: function( request, response ) {
				$.ajax( {
				  url: "http://api.themoviedb.org/3/search/person?api_key=312d63c811b30d655b99270040f594de",
				  dataType: "jsonp",
				  data: {
					query: document.activeElement.value
				  },
				  success: function( data ) {
					var names = new Array();
					for (var i=0; i<data.results.length; i++) {
						names[i] = data.results[i].name;
					}
					response( names );
				  }
				} );
			  },
			  minLength: 2,
			  select: function (event, ui) {
					loadimage(this.parentNode.id);
			  }
			} );
			} );
			
			function loadimage(element) {
				  $.ajax( {
				  url: "http://api.themoviedb.org/3/search/person?api_key=312d63c811b30d655b99270040f594de",
				  dataType: "jsonp",
				  data: {
					//query: element.value
                      query: document.getElementById("txt_actor_"+element).value
				  },
				  success: function( data ) {
					if(data.results && data.results.length!=0) {
                        document.getElementById("img_actor_" + element).src = "http://image.tmdb.org/t/p/w500" + data.results[0].profile_path;
                        document.getElementById("btn_actor_" + element).disabled = false;
                    }
				  }
				} );
			}


			function increaseCount(element, charactername, actorname) {
                $.ajax( {
                    url: "increasecount.php",
                    method: "POST",
                    data: {
                        character: charactername,
                        actor: actorname
                    },
                    success: function( data ) {
                        window.location.reload(true);
                    }
                } );
            }

            function increaseCountOfNewActor(characterName) {
			    var actorName = document.getElementById("txt_actor_"+characterName).value;
                var actorImage = document.getElementById("img_actor_"+characterName).src;

                $.ajax( {
                    url: "increasenewactorcount.php",
                    method: "POST",
                    data: {
                        name: actorName,
                        image: actorImage,
                        character: characterName
                    },
                    success: function( data ) {
                        window.location.reload(true);
                    }
                } );
            }

		</script>

        <h1>YOUR CAST FOR MAHABHARATA</h1>

	</head>
	<body>
	
        <?php

            require('connection.php');

            $characters = $conn->query("SELECT char_id, name, image FROM characters");

            if ($characters->num_rows > 0) {
                while($character = $characters->fetch_assoc()) {

                    echo '<div class="panel panel-success character-panel">';
                        echo '<div class="panel-heading">';
                            echo '<h3 class="panel-title">' . $character['name'] . '</h3>';
                        echo '</div>';
                        echo '<div class="panel-body">';
                            echo '<div class="col-xs-5 col-sm-2 placeholder">';
                                echo '<img class="img-thumbnail photo" src="images/' . $character["image"] . '" />';
                            echo '</div>';

                            $votes = $conn->query("SELECT v.vote_count, a.a_id, a.name, a.image FROM votes v LEFT JOIN actors a ON v.a_id=a.a_id WHERE v.char_id=" . $character["char_id"] . " ORDER BY v.vote_count DESC");
                            if ($votes->num_rows > 0) {
                                while($vote = $votes->fetch_assoc()) {
                                    echo '<div class="col-xs-3 col-sm-2 placeholder">';
                                        echo '<img class="img-thumbnail photo" src='.$vote['image'].' /> <br/>';
                                        echo '<label>'.$vote['name'].'</label>';
                                        echo '<h3><span class="label label-warning">'.$vote['vote_count'].'</span> &nbsp;';
                                        echo '<input type="button" class="btn btn-xs btn-success" value="+1" onclick="increaseCount(this,'.$character["char_id"].','.$vote['a_id'].')" /></h3>';
                                    echo '</div>';
                                }
                            }
                            echo '<div id="'.$character['name'].'" class="col-xs-5 col-sm-2 placeholder">';
                                echo '<img id="img_actor_'.$character['name'].'" class="img-thumbnail photo"/>';
                                echo '<br/><label>Someone else?</label>';
                                echo '<input id="txt_actor_'.$character['name'].'" class="tags" onfocusout="loadimage(\''.$character['name'].'\')" /> &nbsp;';
                                echo '<input type="button" id="btn_actor_'.$character['name'].'" class="btn btn-xs btn-success" disabled value="+1" onclick="increaseCountOfNewActor(\''.$character['name'].'\')" />';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "0 results";
            }
        ?>
		
	<?php
		$conn->close();
	?>
		
	</body>
</html>