<?php

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("config/db.php");

// load the login class
require_once("classes/Login.php");

// load the functions
require_once("includes/functions.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");
	?>


<!DOCTYPE html>
<html>
<?php include "includes/head.php";?>

<body>

    <div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            
			<?php 
			include 'navbar-header.php';
			?>
            <!-- /.navbar-top-links -->
			<?php include 'navbar-top-links.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
        						<?php include 'includes/run_check.php';?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Exporting minION data to the ENA.</h1>
                    <h3><?php echo cleanname($_SESSION['focusrun']); ?></h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<div class="row">
				<div class="col-lg-12">
				minoTour is not intended to be a long term read store for minION data. Rather we hope it wil be useful for real time analsyis. We would like to encourage you to deposit your reads in the appropriate read archive. To this end we are developing tools to generate the appropriate XML files required for read submission to the European Nucleotide Archive. Data submitted to the ENA is exchanged between all International Nucleotide Sequence Database Collaboration (INSDC) partners. At a future time we may support other nucleotide repositories directly.<br><br>
				</div>
				
				
			</div>
			<div class="row">
				<div class="col-lg-12">
				Please complete the fields below and minoTour will automatically generate the XML file required for your submission.<br><br></div>
				
				
			</div>
			<div class="panel panel-default">
				  <div class="panel-body">
					   <form>
  <div class="form-group">
    <label for="user_name">ENA User Name</label>
    <input type="text" class="form-control" id="user_name" placeholder="Your registered ENA user name">
  </div>
    <div class="form-group">
    <label for="user_password">Your ENA account password (we do not store this information).</label>
    <input type="password" class="form-control" id="user_password" placeholder="Your registered ENA user account password">
  </div>
  <div class="form-group">
    <label for="center_name">Center Name</label>
    <input type="text" class="form-control" id="center_name" placeholder="Your registered ENA center name">
  </div>
  <div class="form-group">
    <label for="study_name">Study Name</label>
    <input type="text" class="form-control" id="study_name" placeholder="The name of your study">
  </div>
  <div class="form-group">
  <label for="study_type">Study Type</lable>
  <select class="form-control" id="study_type">
  <option>Whole Genome Sequencing</option>
  <option>Metagenomics</option>
    <option>Transcriptome Analysis</option>
    <option>Resequencing</option>
    <option>Epigenetics</option>
    <option>Synthetic Genomics</option>
    <option>Forensic or Paleo-genomics</option>
    <option>Gene Regulation Study</option>
    <option>Cancer Genomics</option>
    <option>Population Genomics</option>
    <option>RNASeq</option>
    <option>Exome Sequencing</option>
    <option>Pooled Clone Sequencing</option>
    <option>Other</option>
</select>
</div>
<div class="form-group">
<label for="Study Abstract">Study Abstract</label>
<textarea class="form-control" id = "study_abstract" rows="6"></textarea>
</div>
<div class="form-group">
    <label for="study_name">Sample Name</label>
    <input type="text" class="form-control" id="study_name" placeholder="The name of your Sample">
  </div>
  <div class="form-group">
    <label for="taxon_id">Taxon ID</label>
    <input type="text" class="form-control" id="taxon_id" placeholder="The taxon ID for your Sample">
  </div>
  <div class="form-group">
    <label for="library_name">Library Name</label>
    <input type="text" class="form-control" id="library_name" placeholder="Library Name">
  </div>  
  <div class="form-group">
  <label for="library_strategy">Library Strategy</lable>
  <select class="form-control" id="library_strategy">
   <option>WGS (Random sequencing of the whole genome)</option>
   <option>WGA (whole genome amplification to replace some instances of RANDOM)</option>
   <option>WXS (Random sequencing of exonic regions selected from the genome)</option>
   <option>RNA-Seq (Random sequencing of whole transcriptome)</option>
   <option>miRNA-Seq (for micro RNA and other small non-coding RNA sequencing)</option>
   <option>ncRNA-Seq(Non-coding RNA)</option>
   <option>WCS (Random sequencing of a whole chromosome or other replicon isolated from a genome)</option>
   <option>CLONE (Genomic clone based (hierarchical) sequencing)</option>
   <option>POOLCLONE (Shotgun of pooled clones (usually BACs and Fosmids))</option>
   <option>AMPLICON (Sequencing of overlapping or distinct PCR or RT-PCR products)</option>
   <option>CLONEEND (Clone end (5', 3', or both) sequencing)</option>
   <option>FINISHING (Sequencing intended to finish (close) gaps in existing coverage)</option>
   <option>ChIP-Seq (Direct sequencing of chromatin immunoprecipitates)</option>
   <option>MNase-Seq (Direct sequencing following MNase digestion)</option>
   <option>DNase-Hypersensitivity (Sequencing of hypersensitive sites, or segments of open chromatin that are more readily cleaved by DNaseI)
   <option>Bisulfite-Seq (Sequencing following treatment of DNA with bisulfite to convert cytosine residues to uracil depending on methylation status)</option>
   <option>EST (Single pass sequencing of cDNA templates)</option>
   <option>FL-cDNA (Full-length sequencing of cDNA templates)</option>
   <option>CTS (Concatenated Tag Sequencing)</option>
   <option>MRE-Seq (Methylation-Sensitive Restriction Enzyme Sequencing strategy)</option>
   <option>MeDIP-Seq (Methylated DNA Immunoprecipitation Sequencing strategy)</option>
   <option>MBD-Seq (Direct sequencing of methylated fractions sequencing strategy)</option>
   <option>Tn-Seq (for gene fitness determination through transposon seeding)</option>
   <option>VALIDATION (CGHub special request: Independent experiment to re-evaluate putative variants.</option>
   <option>Micro RNA sequencing strategy designed to capturepost-transcriptional RNA elements and include non-coding functional elements)</option>
   <option>FAIRE-seq (Formaldehyde-Assisted Isolation of Regulatory Elements) </option>
   <option>SELEX (Systematic Evolution of Ligands by EXponential enrichment (SELEX) is an in vitro strategy to analyze RNA sequences that perform an activity of interest, most commonly high affinity binding to a ligand)</option>
   <option>RIP-Seq (Direct sequencing of RNA immunoprecipitates (includes CLIP-Seq, HITS-CLIP and PAR-CLI))</option>
   <option>ChiA-PET (Direct sequencing of proximity-ligated chromatin immunoprecipitates)</option>
   <option>RAD-Seq (Restriction site Associated DNA Sequencing is a method for sampling the genomes of multiple individuals in a population using NGS) </option>
   <option>OTHER (Library strategy not listed)</option>
  </select>
  </div>
    <div class="form-group">
  <label for="library_source">Library Source</lable>
  <select class="form-control" id="library_source">
  <option>GENOMIC (Genomic DNA (includes PCR products from genomic DNA))</option>
   <option>TRANSCRIPTOMIC (Transcription products or non genomic DNA (EST, cDNA, RT-PCR, screened libraries))</option>
   <option>METAGENOMIC (Mixed material from metagenome)</option>
   <option>METATRANSCRIPTOMIC (Transcription products from community targets)</option>
   <option>SYNTHETIC (Synthetic DNA)</option>
   <option>VIRAL RNA (Viral RNA)</option>
   <option>OTHER (Other, unspecified, or unknown library source material)</option>
  </select>
  </div>
  <div class="form-group">
  <label for="library_selection">Library Selection</lable>
  <select class="form-control" id="library_selection">
  <option>RANDOM (Random selection by shearing or other method)</option>
   <option>PCR (Source material was selected by designed primers)</option>
   <option>RANDOM PCR (Source material was selected by randomly generated primers)</option>
   <option>RT-PCR (Source material was selected by reverse transcription PCR)</option>
   <option>HMPR (Hypo-methylated partial restriction digest)</option>
   <option>MF (Methyl Filtrated)</option>
   <option>repeat fractionation (replaces: CF-S, CF-M, CF-H, CF-T)</option>
   <option>size fractionation</option>
   <option>MSLL (Methylation Spanning Linking Library)</option>
   <option>cDNA (complementary DNA)</option>
   <option>ChIP (Chromatin immunoprecipitation)</option>
   <option>MNase (Micrococcal Nuclease (MNase) digestion)</option>
   <option>DNAse (Deoxyribonuclease (MNase) digestion)</option>
   <option>Hybrid Selection (Selection by hybridization in array or solution)</option>
   <option>Reduced Representation (Reproducible genomic subsets, often generated by restriction fragment size selection, containing a manageable number of loci to facilitate re-sampling)</option>
   <option>Restriction Digest (DNA fractionation using restriction enzymes)</option>
   <option>5-methylcytidine antibody (Selection of methylated DNA fragments using an antibody raised against 5-methylcytosine or 5-methylcytidine (m5C))</option>
   <option>MBD2 protein methyl-CpG binding domain (Enrichment by methyl-CpG binding domain)</option>
   <option>CAGE (Cap-analysis gene expression)</option>
   <option>RACE (Rapid Amplification of cDNA Ends)</option>
   <option>MDA (Multiple displacement amplification)</option>
   <option>padlock probes capture method (to be used in conjuction with Bisulfite-Seq)</option>
   <option>Inverse rRNA selection (Remove the ribosomal transcripts by inverse selection: you capture them by annealing with specific oligos, also bound to beads, and then discard that)</option>
   <option>Oligo-dT (Select primarily messenger RNA, which conveniently is polyadenylated so these transcripts can be captured with oligo-dT beads)</option>
   <option>other (Other library enrichment, screening, or selection process)</option>
   <option>unspecified (Library enrichment, screening, or selection is not specified)</option>
  </select>
  </div>
  <div class="form-group">
  <label for="library_layout">Library Layout</lable>
  <select class="form-control" id="library_layout">
  <option>Single</option>
  <option>Paired</option>
  </select>
  </div>
  <div class="form-group">
  <label for="sequencing_kit">Sequencing Kit</lable>
  <select class="form-control" id="sequencing_kit">
  <option>Dev-MAP003</option>
  <option>SQK-MAP003</option>
  <option>SQK-MAP004</option>
  </select>
  </div>
  <div class="form-group">
  <label for="sequencing_protocol">Sequencing Protocol</lable>
  <select class="form-control" id="sequencing_protocol">
  <option>gDNA</option>
  <option>cDNA</option>
  </select>
  </div>
  
  <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
<div class="row">
				<div class="col-lg-12">
				
				

<?php
				date_default_timezone_set('UTC');
				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
				if (!$mindb_connection->connect_errno) {
					$basicrunsql = "SELECT asic_id,AVG(asic_temp) as asic_temp_avg,std(asic_temp) as asic_temp_std,exp_script_purpose,exp_start_time,flow_cell_id,AVG(heatsink_temp) as heatsink_temp_avg,std(heatsink_temp) as heatsink_temp_std,run_id,version_name FROM tracking_id group by device_id,flow_cell_id,asic_id;";
					$basicrunresults = $mindb_connection->query($basicrunsql);
					
					if ($basicrunresults->num_rows == 1){
					$basicrunresults_row = $basicrunresults->fetch_object();
					
					echo "Experiment Script Purpose<br>";
					echo $basicrunresults_row->exp_script_purpose . "<br>";
					echo "Experiment Start Date/Time<br>";
					echo gmdate("H:i:s Y-m-d", $basicrunresults_row->exp_start_time) . "<br>";
					echo "ASIC ID<br>";
					echo $basicrunresults_row->asic_id . "<br>";
					echo "Average ASIC Temp (stand var)<br>";
					echo $basicrunresults_row->asic_temp_avg . " (" . $basicrunresults_row->asic_temp_std . ")<br>";
					echo "Average Heatsink Temp (stand var)<br>";
					echo $basicrunresults_row->heatsink_temp_avg . " (" . $basicrunresults_row->heatsink_temp_std . ")<br>";
					echo "Run ID<br>";
					echo $basicrunresults_row->run_id .  "<br>";
					echo "MinKNOW Version<br>";
					echo $basicrunresults_row->version_name .  "<br>";
	
					}
					
					echo "<br>Read Listing - Run XML:<br>";
					?>
					
					<pre>
&lt;?xml version="1.0" encoding="UTF-8"?>
&lt;RUN_SET  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ftp://ftp.sra.ebi.ac.uk/meta/xsd/sra_1_5/SRA.run.xsd">
	&lt;RUN alias="<?php echo $_GET["run_name"];?>" center_name="<?php echo $_GET["run_center"];?>" run_center="<?php echo $_GET["run_center"];?>" run_date="<?php echo $_GET["run_date"];?>">
	&lt;EXPERIMENT_REF refname="<?php echo $_GET["exp_name"];?>"/>
	&lt;DATA_BLOCK member_name="TODO: FOR DEMULTIPLEXED DATA ONLY (see note below)">
		&lt;FILES>
		<?php
		$readreport = "SELECT file_path,md5sum,basecalled_template.basename_id as template_id, basecalled_complement.basename_id as complement_id, basecalled_2d.basename_id as 2d_id FROM tracking_id left join basecalled_template using (basename_id) left join basecalled_complement using (basename_id) left join basecalled_2d using (basename_id);";
					$readreportresults = $mindb_connection->query($readreport);
					foreach ($readreportresults as $row) {
						$chunks = explode("/",$row['file_path']);
						$result = count($chunks);
						echo "&lt;FILE filename=\"" . $chunks[$result-1] . "\" ";
						echo "filetype=\"OxfordNanopore_native\" ";
						echo "checksum_method=\"MD5\" checksum=\"".$row[md5sum]."\" ";
						if (in_array("pass", $chunks)) {
						    echo "pass_fail=\"pass\" ";
						}elseif(in_array("fail", $chunks)) {
						    echo "pass_fail=\"fail\" ";
						}
						echo "read_type=\"";
						if ($row['2d_id'] > 0) {
							echo "2d";
						}elseif ($row['complement_id'] > 0) {
							echo "complement";
						}elseif ($row['template_id'] > 0) {
							echo "template";
						}else{
							echo "failed";
						}
						echo "\"/>";
						echo "\n";
					}

		?>
			&lt;/FILES>		
        &lt;/DATA_BLOCK>
    &lt;/RUN>
    <!-- If you are submitting more than one run, replicate the block <RUN> to </RUN> here, 
        as many times as necessary. -->
&lt;/RUN_SET>					
					</pre>
					
					
					
					
					
					<?php
				}
?>
<br>Study Description- Study XML:<br>
<pre>
&lt;?xml version="1.0" encoding="UTF-8"?>
&lt;STUDY_SET xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="ftp://ftp.sra.ebi.ac.uk/meta/xsd/sra_1_5/SRA.study.xsd">
    &lt;STUDY alias="<?php echo $_GET["study_alias"];?>" 
        center_name="<?php echo $_GET["center_name"];?>">
        &lt;DESCRIPTOR>
            &lt;STUDY_TITLE><?php echo $_GET["study_title"];?>&lt;/STUDY_TITLE>
            &lt;STUDY_TYPE existing_study_type="<?php echo $_GET["study_type"];?>"/>
            &lt;STUDY_ABSTRACT><?php echo $_GET["study_abstract"];?>&lt;/STUDY_ABSTRACT>
        &lt;/DESCRIPTOR>
        &lt;STUDY_ATTRIBUTES>
            &lt;STUDY_ATTRIBUTE>
                &lt;TAG>TODO: TAG NAME&lt;/TAG>
                &lt;VALUE>TODO: TAG VALUE&lt;/VALUE>
            &lt;/STUDY_ATTRIBUTE>
            &lt;STUDY_ATTRIBUTE>
                &lt;TAG>TODO: TAG NAME&lt;/TAG>
                &lt;VALUE>TODO: TAG VALUE&lt;/VALUE>
            &lt;/STUDY_ATTRIBUTE>
            <!-- You can generate your own fields and values here using STUDY_ATTRIBUTE 
                tag-value pairs. Please delete any unused attributes and add as many as 
                required. -->
        &lt;/STUDY_ATTRIBUTES>
    &lt;/STUDY>
    <!-- If you are submitting more than one study, replicate the block <STUDY> to </STUDY> 
        here, as many times as necessary. -->
&lt;/STUDY_SET>
<!-- Controlled vocabulary for existing_study_type:
    Whole Genome Sequencing
    Metagenomics
    Transcriptome Analysis
    Resequencing
    Epigenetics
    Synthetic Genomics
    Forensic or Paleo-genomics
    Gene Regulation Study
    Cancer Genomics
    Population Genomics
    RNASeq
    Exome Sequencing
    Pooled Clone Sequencing
    Other
    If using "Other" please add new_study_type="TODO: add own term" attribute
-->
</pre>
<br>Sample Description- Sample XML:<br>
<pre>
&lt;?xml version="1.0" encoding="UTF-8"?>
&lt;SAMPLE_SET xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="ftp://ftp.sra.ebi.ac.uk/meta/xsd/sra_1_5/SRA.sample.xsd">
    &lt;SAMPLE alias="<?php echo $_GET["sample_alias"];?>" 
        center_name="<?php echo $_GET["center_name"];?>">                     
        &lt;TITLE><?php echo $_GET["sample_title"];?>&lt;/TITLE>
        &lt;SAMPLE_NAME>
            &lt;TAXON_ID><?php echo $_GET["taxon_id"];?>
                 &lt;/TAXON_ID>
            <!-- For complete prokaryotic genomes, a taxid should be generate for the strain. 
                 Please contact us so we can generate this on your behalf. -->
            &lt;SCIENTIFIC_NAME><?php echo $_GET["scientific_name"];?>&lt;/SCIENTIFIC_NAME>
            &lt;COMMON_NAME><?php echo $_GET["common_name"];?>&lt;/COMMON_NAME>
        &lt;/SAMPLE_NAME>
        &lt;DESCRIPTION><?php echo $_GET["sample_description"];?>&lt;/DESCRIPTION>
        &lt;SAMPLE_ATTRIBUTES>
            &lt;SAMPLE_ATTRIBUTE>
                &lt;TAG>TODO: TAG NAME&lt;/TAG>
                &lt;VALUE>TODO: TAG VALUE&lt;/VALUE>
                &lt;UNITS>TODO: OPTIONAL UNIT&lt;/UNITS>
            &lt;/SAMPLE_ATTRIBUTE>
            &lt;SAMPLE_ATTRIBUTE>
                &lt;TAG>TODO: TAG NAME&lt;/TAG>
                &lt;VALUE>TODO: TAG VALUE&lt;/VALUE>
                &lt;UNITS>TODO: OPTIONAL UNIT&lt;/UNITS>
            &lt;/SAMPLE_ATTRIBUTE>
            <!-- You can generate your own fields and values here using SAMPLE_ATTRIBUTE 
                tag-value pairs. An example tag could be "Isolation Source" and the value 
                could be "Seawater". You can also use the UNITS element to include 
                scientific units. E.g., TAG "Age" VALUE "5" UNITS "Years". Please refer
                to online documentation for further help with sample tag-value pairs.
                Please delete any unused attributes and add as many as required. -->
        &lt;/SAMPLE_ATTRIBUTES>
    &lt;/SAMPLE>
    <!-- If you are submitting more than one sample, replicate the block <SAMPLE> to </SAMPLE> 
         here, as many times as necessary. -->
&lt;/SAMPLE_SET> 
</pre>
<br>Experiment Description- Experiment XML:<br>
<pre>
&lt;?xml version="1.0" encoding="UTF-8"?>
&lt;EXPERIMENT_SET xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="ftp://ftp.sra.ebi.ac.uk/meta/xsd/sra_1_5/SRA.experiment.xsd">
    &lt;EXPERIMENT alias="<?php echo $_GET["experiment_alias"];?>"
        center_name="<?php echo $_GET["center_name"];?>">
        &lt;TITLE><?php echo $_GET["experiment_title"];?>&lt;/TITLE>
        &lt;STUDY_REF refname="<?php echo $_GET["study_ref"];?>"/>
        &lt;DESIGN>
            &lt;DESIGN_DESCRIPTION>TODO: DETAILS ABOUT THE SETUP AND GOALS OF THE 
                EXPERIMENT AS SUPPLIED BY INVESTIGATOR&lt;/DESIGN_DESCRIPTION>
            &lt;SAMPLE_DESCRIPTOR refname="TODO: SAMPLE ALIAS OF RELEVANT SAMPLE 
                OBJECT"/>
            &lt;LIBRARY_DESCRIPTOR>
                &lt;LIBRARY_NAME>TODO: NAME OF LIBRARY&lt;/LIBRARY_NAME>
                &lt;LIBRARY_STRATEGY>TODO: CHOOSE FROM CONTROLLED VOCABULARY AT 
                    END OF XML&lt;/LIBRARY_STRATEGY>
                &lt;LIBRARY_SOURCE>TODO: CHOOSE FROM CONTROLLED VOCABULARY AT 
                    END OF XML &lt;/LIBRARY_SOURCE>
                &lt;LIBRARY_SELECTION>TODO: CHOOSE FROM CONTROLLED VOCABULARY AT 
                    END OF XML &lt;/LIBRARY_SELECTION>
                &lt;LIBRARY_LAYOUT>
                    &lt;TODO: CHOOSE LIBRARY LAYOUT FROM CONTROLLED VOCABULARY AT END OF XML/>
                &lt;/LIBRARY_LAYOUT>
                &lt;LIBRARY_CONSTRUCTION_PROTOCOL>TODO: PROTOCOL BY WHICH THE LIBRARY WAS
                    CONSTRUCTED&lt;/LIBRARY_CONSTRUCTION_PROTOCOL>
            &lt;/LIBRARY_DESCRIPTOR>         
        &lt;/DESIGN>
        &lt;PLATFORM>
            &lt;OXFORD_NANOPORE>
                &lt;INSTRUMENT_MODEL>MinION&lt;/INSTRUMENT_MODEL>
            &lt;/OXFORD_NANOPORE>
        &lt;/PLATFORM>
        &lt;PROCESSING/>
    &lt;/EXPERIMENT>
    <!-- If you are submitting more than one experiment, replicate the block <EXPERIMENT> 
        to </EXPERIMENT> here, as many times as necessary. -->
&lt;/EXPERIMENT_SET>
<!-- Controlled vocabulary for LIBRARY_LAYOUT
   SINGLE
   PAIRED
-->
<!-- Controlled vocabulary for PLATFORM
   LS454
   ILLUMINA
   COMPLETE_GENOMICS
   PACBIO_SMRT
   ION_TORRENT
   OXFORD_NANOPORE
   CAPILLARY
-->
<!--Controlled vocabulary for LS454 INSTRUMENT_MODEL:
   454 GS
   454 GS 20454 GS FLX
   454 GS FLX+
   454 GS FLX Titanium
   454 GS Junior
   unspecified
-->
<!-- Controlled vocabulary for ILLUMINA INSTRUMENT_MODEL:
   Illumina Genome Analyzer
   Illumina Genome Analyzer II
   Illumina Genome Analyzer IIx
   Illumina HiSeq 2500
   Illumina HiSeq 2000
   Illumina HiSeq 1500
   Illumina HiSeq 1000
   Illumina MiSeq
   Illumina HiScanSQ 
   HiSeq X Ten
   NextSeq 500
   unspecified
-->
<!-- Controlled vocabulary for COMPLETE_GENOMICS INSTRUMENT_MODEL:
   Complete Genomics
   unspecified
-->
<!-- Controlled vocabulary for PACBIO_SMRT INSTRUMENT_MODEL:
   PacBio RS
   PacBio RS II
   unspecified
-->
<!-- Controlled vocabulary for ION_TORRENT INSTRUMENT_MODEL:
   Ion Torrent PGM
   Ion Torrent Proton
   unspecified
-->
<!-- Controlled vocabulary for OXFORD_NANOPORE INSTRUMENT_MODEL:
   MinION
   GridION
   unspecified
-->
<!-- Controlled vocabulary for CAPILLARY INSTRUMENT_MODEL:
   AB 3730xL Genetic Analyzer
   AB 3730 Genetic Analyzer
   AB 3500xL Genetic Analyzer
   AB 3500 Genetic Analyzer
   AB 3130xL Genetic Analyzer
   AB 3130 Genetic Analyzer
   AB 310 Genetic Analyzer
-->
<!-- Controlled vocabulary for LIBRARY STRATEGY (description in parentheses):
   WGS (Random sequencing of the whole genome)
   WGA (whole genome amplification to replace some instances of RANDOM)
   WXS (Random sequencing of exonic regions selected from the genome)
   RNA-Seq (Random sequencing of whole transcriptome)
   miRNA-Seq (for micro RNA and other small non-coding RNA sequencing)
   ncRNA-Seq(Non-coding RNA)
   WCS (Random sequencing of a whole chromosome or other replicon isolated 
   from a genome)
   CLONE (Genomic clone based (hierarchical) sequencing)
   POOLCLONE (Shotgun of pooled clones (usually BACs and Fosmids))
   AMPLICON (Sequencing of overlapping or distinct PCR or RT-PCR products)
   CLONEEND (Clone end (5', 3', or both) sequencing)
   FINISHING (Sequencing intended to finish (close) gaps in existing coverage)
   ChIP-Seq (Direct sequencing of chromatin immunoprecipitates)
   MNase-Seq (Direct sequencing following MNase digestion)
   DNase-Hypersensitivity (Sequencing of hypersensitive sites, or segments of
   open chromatin that are more readily cleaved by DNaseI)
   Bisulfite-Seq (Sequencing following treatment of DNA with bisulfite to 
   convert cytosine residues to uracil depending on methylation status)
   EST (Single pass sequencing of cDNA templates)
   FL-cDNA (Full-length sequencing of cDNA templates)
   CTS (Concatenated Tag Sequencing)
   MRE-Seq (Methylation-Sensitive Restriction Enzyme Sequencing strategy)
   MeDIP-Seq (Methylated DNA Immunoprecipitation Sequencing strategy)
   MBD-Seq (Direct sequencing of methylated fractions sequencing strategy)
   Tn-Seq (for gene fitness determination through transposon seeding)
   VALIDATION (CGHub special request: Independent experiment to re-evaluate putative variants.
   Micro RNA sequencing strategy designed to capturepost-transcriptional RNA elements and include non-coding functional elements)
   FAIRE-seq (Formaldehyde-Assisted Isolation of Regulatory Elements) 
   SELEX (Systematic Evolution of Ligands by EXponential enrichment (SELEX) is an 
   in vitro strategy to analyze RNA sequences that perform an activity of interest, most
   commonly high affinity binding to a ligand)
   RIP-Seq (Direct sequencing of RNA immunoprecipitates (includes CLIP-Seq, HITS-CLIP and PAR-CLI))
   ChiA-PET (Direct sequencing of proximity-ligated chromatin immunoprecipitates)
   RAD-Seq (Restriction site Associated DNA Sequencing is a method for sampling the genomes of multiple individuals in a population
   using NGS) 
   OTHER (Library strategy not listed)
-->
<!-- Controlled vocabulary for LIBRARY SOURCE (description in parentheses):
   GENOMIC (Genomic DNA (includes PCR products from genomic DNA))
   TRANSCRIPTOMIC (Transcription products or non genomic DNA (EST, cDNA, RT-PCR, 
   screened libraries))
   METAGENOMIC (Mixed material from metagenome)
   METATRANSCRIPTOMIC (Transcription products from community targets)
   SYNTHETIC (Synthetic DNA)
   VIRAL RNA (Viral RNA)
   OTHER (Other, unspecified, or unknown library source material)
-->
<!-- Controlled vocabulary for LIBRARY SELECTION (description in parentheses):
   RANDOM (Random selection by shearing or other method)
   PCR (Source material was selected by designed primers)
   RANDOM PCR (Source material was selected by randomly generated primers)
   RT-PCR (Source material was selected by reverse transcription PCR)
   HMPR (Hypo-methylated partial restriction digest)
   MF (Methyl Filtrated)
   repeat fractionation (replaces: CF-S, CF-M, CF-H, CF-T)
   size fractionation
   MSLL (Methylation Spanning Linking Library)
   cDNA (complementary DNA)
   ChIP (Chromatin immunoprecipitation)
   MNase (Micrococcal Nuclease (MNase) digestion)
   DNAse (Deoxyribonuclease (MNase) digestion)
   Hybrid Selection (Selection by hybridization in array or solution)
   Reduced Representation (Reproducible genomic subsets, often generated by 
   restriction fragment size selection, containing a 
   manageable number of loci to facilitate re-sampling)
   Restriction Digest (DNA fractionation using restriction enzymes)
   5-methylcytidine antibody (Selection of methylated DNA fragments using an 
   antibody raised against 5-methylcytosine or 
   5-methylcytidine (m5C))
   MBD2 protein methyl-CpG binding domain (Enrichment by methyl-CpG binding domain)
   CAGE (Cap-analysis gene expression)
   RACE (Rapid Amplification of cDNA Ends)
   MDA (Multiple displacement amplification)
   padlock probes capture method (to be used in conjuction with Bisulfite-Seq)
   Inverse rRNA selection (Remove the ribosomal transcripts by inverse selection: you capture them by annealing with specific oligos, 
   also bound to beads, and then discard that)
   Oligo-dT (Select primarily messenger RNA, which conveniently is polyadenylated so these transcripts can be captured with oligo-dT beads)
   other (Other library enrichment, screening, or selection process) 
   unspecified (Library enrichment, screening, or selection is not specified) 
-->
</pre>

				</div>
			</div>

				  </div>
			</div>
						
	
	    </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- Page-Level Plugin Scripts - Dashboard -->
			    <script type="text/javascript" src="js/pnotify.custom.min.js"></script>
			    <script type="text/javascript">
				PNotify.prototype.options.styling = "fontawesome";
				</script>
    <script src="js/plugins/morris/raphael-2.1.0.min.js"></script>
    <script src="js/plugins/morris/morris.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>

    <!-- Page-Level Demo Scripts - Dashboard - Use for reference -->
    <script src="js/demo/dashboard-demo.js"></script>

     <script>
        $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
        var auto_refresh = setInterval(function ()
            {
            $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
            //eval(document.getElementById("infodiv").innerHTML);
            }, 10000); // refresh every 5000 milliseconds
    </script>
	
<?php include "includes/reporting.php";?>
</body>

</html>
<?php 
} else {
	
	    // the user is not logged in. you can do whatever you want here.
	    // for demonstration purposes, we simply show the "you are not logged in" view.
	    include("views/not_logged_in.php");
	}
	
	?>
