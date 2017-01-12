		<!-- Clips Modal HTML -->
		<div id="clipsmodal" style="display: none;">
			<section>
				<ul class="redactor_clips_box">
					<li>
						<a href="#" class="redactor_clip_link">Intro</a>

						<div class="redactor_clip" style="display: none;">
							<p class="intro">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Harum, deleniti vel maiores placeat doloribus tempore tenetur explicabo error! Officiis, odio.</p>
						</div>
					</li>	
					<li>
						<a href="#" class="redactor_clip_link">Panel</a>

						<div class="redactor_clip" style="display: none;">
							<div class="panel radius">
								<h5>Panel header</h5>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cumque, placeat.</p>
							</div>
						</div>
					</li>
					<li>
						<a href="#" class="redactor_clip_link">Gevolgde bij- en nascholing</a>

						<div class="redactor_clip" style="display: none;">
							<h5>2 jarige opleiding 'Klinische Psycho-Neuro-Immunologie', bij Natura Foundation.</h5>
							<p><em>Docenten: Leo Pruimboom, Frits Muskiet, Tom Fox, e.m</em></p>
						</div>
					</li>
					
					<li>
						<a href="#" class="redactor_clip_link">Blockquote</a>

						<div class="redactor_clip" style="display: none;">
							<p class='blockquote'>Een gezond lichaam kent geen klachten.</p>
						</div>
					</li>
					
					<li>
						<a href="#" class="redactor_clip_link">Citaat</a>

						<div class="redactor_clip" style="display: none;">
							<p class="quote_inside">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Harum, deleniti vel maiores placeat doloribus tempore tenetur explicabo error! Officiis, odio.<br /><span>Voornaam Achternaam</span></p>
						</div>
					</li>	
					
					<li>
						<a href="#" class="redactor_clip_link">Internal comment</a>

						<div class="redactor_clip" style="display: none;">
							<p class="comment"><strong><?=$this->loggedInUserName?>:</strong> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Harum, deleniti vel maiores placeat doloribus tempore tenetur explicabo error! Officiis, odio.</p>
						</div>
					</li>
										
				</ul>
			</section>
			<footer>
				<button class="redactor_modal_btn redactor_btn_modal_close">Close</button>
			</footer>
		</div>



<!-- My Images Modal -->
<div id="myimages" style="display: none;">
    <section>
        <label>Enter a text</label>
        <textarea id="myimages-textarea" style="width: 100%; height: 150px;"></textarea>
    </section>
    <footer>
        <button class="redactor_modal_btn redactor_modal_action_btn" id="myimages-insert">Insert</button>
        <button class="redactor_modal_btn redactor_btn_modal_close">Close</button>
    </footer>
</div>

		

		<!-- MyLink Modal File HTML --> 
		<?php if(isset($this->WysiwygFiles)): ?>       
		<div id="mylink_file" style="display: none;">
			<div id="redactor_modal_content">
								
				<form id="redactorInsertLinkForm" method="post" action="">
					<label id='label'>File</label>
					<select id='mylink_file-file'>
						<?php if(isset($this->WysiwygFiles) && is_array($this->WysiwygFiles)): ?>
						<?php foreach($this->WysiwygFiles as $file): ?>
						<option value='<?=$file['filename'].'.'.$file['ext']?>'><?=$file['name']?></option>
						<?php endforeach; ?>
						<?php endif; ?>						
					</select>
					
					<label>Tekst</label><input type="text" class="redactor_input redactor_link_text" id="mylink_file-linktext" />
					<label><input type="checkbox" id="mylink_file-linkblank">Open bestand in nieuw venster</label>
				</form>				
				
			</div>
			<div id="redactor_modal_footer">
				<a href="javascript:void(null);" class="redactor_modal_btn redactor_btn_modal_close">Sluiten</a>
				<a href="javascript:void(null);" id='mylink_file-insert' class="redactor_modal_btn">Invoegen</a>
			</div>
		</div>
		<?php endif; ?>
		
				