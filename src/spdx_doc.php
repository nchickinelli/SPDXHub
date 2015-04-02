<?php
	$title = "Drill Down";
    include("function/_header.php");
    include("function/spdx_doc.php");
    include("function/creator.php");
    include("function/package.php");
    include("function/package_files.php");
    include("function/license.php");
    include("function/tree.php");
    
    $spdxId = $_GET["doc_id"];
    if(array_key_exists('action',$_POST)){
        if($_POST["action"] == "update"){
            updateSPDX_Doc($spdxId, $_POST["document_comment"], $_POST["spdx_version"], $_POST["data_license"], $_POST["creator"], $_POST["creator_comments"]);
            updatePackage($spdxId, 
                          $_POST["package_name"], 
                          $_POST["package_version"], 
                          $_POST["package_download_location"], 
                          $_POST["package_summary"], 
                          $_POST["package_file_name"],
                          $_POST["package_supplier"],
                          $_POST["package_originator"],
                          $_POST["package_description"],
                          $_POST["package_copyright_text"],
                          $_POST["package_license_concluded"]);
        }
    }
    $doc = mysql_fetch_assoc(getSPDX_Doc($spdxId));
    $licCounts = getLicenseCounts($spdxId);
?>
<style>
    a.tooltip {outline:none; }
    a.tooltip strong {line-height:30px;}
    a.tooltip:hover {text-decoration:none;} 
    a.tooltip span {
        z-index:10;display:none; padding:14px 20px;
        margin-top:-30px; margin-left:28px;
        width:240px; line-height:16px;
    }
    a.tooltip:hover span{
        display:inline; position:absolute; color:#111;
        border:1px solid #DCA; background:#fffAF0;}
    .callout {z-index:20;position:absolute;top:30px;border:0;left:-12px;}
        
    /*CSS3 extras*/
    a.tooltip span
    {
        border-radius:4px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
            
        -moz-box-shadow: 5px 5px 8px #CCC;
        -webkit-box-shadow: 5px 5px 8px #CCC;
        box-shadow: 5px 5px 8px #CCC;
    }
    .legend li span {
	    width: 1em;
	    height: 1em;
	    display: inline-block;
	    margin-right: 5px;
	}
	.legend {
	    list-style: none;    
	}
</style>
<script src="js/Chart.js"></script>
<script>
    $(document).ready(function() {
    	$("#edit_doc").click(function () {
    		$('.edit').show();
        	$('.view').hide();
        });
    });
</script>
<div class="container">
    <?php 
        if(array_key_exists('action',$_POST)) {
            if($_POST["action"] == "update") {
                echo '<div align="center"><h4><p class="text-success">Successfully Updated Document</p></h4></div>';
            }
        }
    ?>
    <form id="spdx_form" action="spdx_doc.php?doc_id=<?php echo $spdxId; ?>" method="post">
        <input type="hidden" name="action" value="update"/>
        <table id="tblMain" class="table table-bordered table-striped table-doc">
            <thead>
                <tr>
                    <th colspan=2><?php echo $doc["document_name"]; ?>
                        <div style="display:inline-block;float:right;">
                            <button id="download_top" type="button"  class="btn btn-primary" onclick="window.open('download.php?doc_id=<?php echo $spdxId; ?>&format=RDF&doc_name=<?php echo $doc["name"];?>','_blank');">Download RDF</button>
                            <button id="download_top" type="button"  class="btn btn-primary" onclick="window.open('download.php?doc_id=<?php echo $spdxId; ?>&format=TAG&doc_name=<?php echo $doc["name"];?>','_blank');">Download TAG</button>
                            <button id="download_top" type="button"  class="btn btn-primary" onclick="window.open('download.php?doc_id=<?php echo $spdxId; ?>&format=JSON&doc_name=<?php echo $doc["name"];?>','_blank');">Download JSON</button>
                            <button id="edit_doc"     type="button"  class="btn btn-primary view"/>Edit</button>
                            <button id="save_doc"     type="submit"  class="btn btn-primary edit" style="display:none;">Save</button>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td title="Version of the SPDX spcification used to create this document.">Version</td>
                    <td class="edit" style="display:none;">
                        <textarea name="spdx_version" class='form-control'><?php echo $doc["version"]; ?></textarea>
                       </td>
                    <td class="view"><?php echo $doc["version"]; ?></td>
                </tr>
                <tr>
                    <td title="License of the content within this SPDX document.">Data License</td>
                    <td class="edit" style="display:none;">
                        <textarea name="data_license" class='form-control'><?php echo $doc["data_license"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["data_license"]; ?></td>
                </tr>
                <tr>
                    <td title="Additional comments for this SPDX document.">Document Comment</td>
                    <td class="edit" style="display:none;">
                        <textarea name="document_comment"  class='form-control'><?php echo $doc["document_comment"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["document_comment"]; ?></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan=2 id = "CreatingInfo">Creation Information</th>
                </tr>
            </thead>
            <tbody id = "CreatingInfoContent">
                <tr >
                    <td title="Who created this SPDX document.">Creator</td>
                    <td class="edit" style="display:none;">
                        <textarea name="creator" class='form-control'><?php echo $doc["creator"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["creator"]; ?></td>
                </tr>
                <tr>
                    <td title="When was this SPDX document created.">Created</td>
                    <td><?php echo date('m/d/Y', strtotime($doc["created_date"])); ?></td>
                </tr>
                <tr>
                    <td title="When was this document last updated.">Updated</td>
                    <td><?php echo date('m/d/Y', strtotime($doc["created_date"])); ?></td>
                </tr>
                <tr>
                    <td title="Additional comments from during the creation of this document.">Creator Comment</td>
                    <td class="edit" style="display:none;">
                        <textarea name="creator_comments" class='form-control'><?php echo $doc["creator_comment"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["creator_comment"]; ?></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan=2 id="PackInfo">Package Information</th>
                </tr>
            </thead>
            <tbody id="PackInfoContent">
                <tr>
                    <td title="Name of the package this SPDX document was created for.">Package Name</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_name" class='form-control'><?php echo $doc["name"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["name"]; ?></td>
                </tr>
                <tr>
                    <td title="Version number of the package this SPDX document was created for.">Package Version</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_version" class='form-control'><?php echo $doc["version"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["version"]; ?></td>
                </tr>
                <tr>
                    <td title="Where this package was downloaded from (URL).">Package Download Location</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_download_location" class='form-control'><?php echo $doc["download_location"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["download_location"]; ?></td>
                </tr>
                <tr>
                    <td title="This field is a short description of the package.">Package Summary</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_summary" class='form-control'><?php echo $doc["summary"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["summary"]; ?></td>
                </tr>
                <tr>
                    <td title="Name of the file that contains this package.">Package File Name</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_file_name" class='form-control'><?php echo $doc["filename"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["filename"]; ?></td>
                </tr>
                <tr>
                    <td title="Original source of this package.">Package Supplier</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_supplier" class='form-control'><?php echo $doc["supplier"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["supplier"]; ?></td>
                </tr>
                <tr>
                    <td title="If this SPDX document came from a different source, what was that source.">Package Originator</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_originator" class='form-control'><?php echo $doc["originator"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["originator"]; ?></td>
                </tr>
                <tr>
                    <td title="Unique identifier for the original package archive file.">Package Checksum</td>
                    <td><?php echo $doc["checksum"]; ?></td>
                </tr>
                <tr>
                    <td title="Unique identifier for the package as a whole.">Package Verification Code</td>
                    <td><?php echo $doc["verificationcode"]; ?></td>
                </tr>
                <tr>
                    <td title="Short description of the package.">Package Description</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_description" class='form-control'><?php echo $doc["description"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["description"]; ?></td>
                </tr>
                <tr>
                    <td title="Any text related to a copyright notice within this package.">Package Copyright Text</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_copyright_text" class='form-control'><?php echo $doc["package_copyright_text"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["package_copyright_text"]; ?></td>
                </tr>
                <tr>
                    <td title="License declared by the authors of this package.">License Declared</td>
                    <td><?php echo $doc["license_declared"]; ?></td>
                </tr>
                <tr>
                    <td title="The governing license of this package.">Package License Concluded</td>
                    <td class="edit" style="display:none;">
                        <textarea name="package_license_concluded" class='form-control'><?php echo $doc["license_concluded"]; ?></textarea>
                    </td>
                    <td class="view"><?php echo $doc["license_concluded"]; ?></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan=2 id="tfiles">Files</th>
                </tr>
            </thead>
            <tbody id="tfilesContent">
                    <tr>
            <td colspan="2">
                    <?php
                        $files = getPackageFiles($spdxId);
                        $all_files = array();
                        $file_licenses = array();
                        while($row = mysql_fetch_assoc($files)) {
                            $all_files[$row['relative_path']]=$row['id'];
                            $file_licenses[$row['relative_path']]['license_id'] = $row['license_id'];
                            $file_licenses[$row['relative_path']]['license_name'] = $row['license_name'];
                        }
                        
                        $mAllTrees = array();
    
                       foreach($all_files as $file=> $fileId){
                            if(strpos($file,'/') != FALSE){
                               $path = substr($file,0,strrpos($file,'/'));
                               $fileName = substr($file,strrpos($file,'/')+1);
                               $root = substr($file,0,strpos($file,'/'));
                               
                               if(array_key_exists($root,$mAllTrees))
                                    $tree = $mAllTrees[$root];
                                else{
                                    $tree = new Tree();
                                    $tree->setSpdxId($spdxId);
                                    $mAllTrees[$root] = $tree;
                                }
                                   
                                                
                               if(!$tree->hasPath($path)) {
                                  $tree->createPath($path);
                               }
                               $tree->addFileToPath($path,
                                                    $fileName . ' - <a href="file.php?file_id=' . $all_files[$file]. '&doc_id=' . $spdxId . '">View File Details</a> - ' .
                                                        $file_licenses[$file]['license_name'] . ' - <a href="license.php?license_id=' . $file_licenses[$file]['license_id'] . '&doc_id=' . $spdxId . '">View License Details</a>',
                                                    $all_files[$file]);
                            }
                            else{
                                $tree = new Tree();
                                $tree->setSpdxId($spdxId);
                                $tree->createNode($file . ' - <a href="file.php?file_id=' . $all_files[$file]. '&doc_id=' . $spdxId . '">View File Details</a> - ' . 
                                        $file_licenses[$file]['license_name'] . ' - <a href="license.php?license_id=' . $file_licenses[$file]['license_id'] . '&doc_id=' . $spdxId . '">View License Details</a>'
                                                  ,null);
                                $tree->addFieldId($file,$all_files[$file]);
                                $mAllTrees[$file] = $tree;
                            }
                       }
                       
                       if(count($mAllTrees) > 0){
                          $html = '';
                          $html = $html.'<div class="tree"><ul>';
                          foreach($mAllTrees as $root => $iTree){
                            $html = $html.$iTree->printTreeNew($iTree->getRoot());
                          }
                          $html = $html.'</ul></div>';
                          echo $html;
                       }
                    ?>
                    <td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <td colspan="2">License Breakdown</td>
                </tr>
            </thead>
            <tbody>
            	<tr>
            		<td colspan="2">
            			<div align="center">
            				<canvas id="licChart" width="400" height="400"></canvas>
            			</div>
            			<div id="legend"></div>
            		</td>
            	</tr>
            </tbody>
        </table>
    </form>
</div>

<script>
    var pieData = [
       <?php $count = 0;?>
       <?php while($row = mysql_fetch_assoc($licCounts)):?>
            {
                value: <?php echo $row['numLicenses']; ?>,
                label: "<?php echo str_replace(array("\r\n", "\n", "\r"), '', $row['license_name']); ?>",
                color: getRandomColor(),
                highlight: '#D8D8D8'
            }
            <?php $count = $count +1;?>
            <?php if($count != mysql_num_rows($licCounts)):?>,<?php endif;?>
       <?php endwhile;?>
    ];
    window.onload = function(){
        var ctx = document.getElementById("licChart").getContext("2d");
        var options = {
                legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%> !important;\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
        };
        var myPie = new Chart(ctx).Pie(pieData,options);
        var legend = myPie.generateLegend();
        $("#legend").html(legend);
    };
    function getRandomColor() {
        var letters = '0123456789ABCDEF'.split('');
        var color = '#';
        for (var i = 0; i < 6; i++ ) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
</script>
<?php
    include("function/_footer.php");
?>

