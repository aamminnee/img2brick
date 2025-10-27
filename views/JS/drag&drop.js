class DragDropController {
    constructor(isValidUser) {
        this.dropZone = document.getElementById("drop-zone");
        this.fileInput = document.getElementById("fileInput");
        this.fileLabel = document.getElementById("fileLabel");
        this.message = document.getElementById("message");
        this.continueButton = document.getElementById("continueButton");
        this.preview = document.getElementById("preview");

        this.selectedFile = null;
        this.isValidUser = isValidUser;

        this.setupEvents();
    }

    setupEvents() {
        // Drag & Drop
        this.dropZone.addEventListener("dragover", e => { e.preventDefault(); this.dropZone.classList.add("dragover"); });
        this.dropZone.addEventListener("dragleave", () => this.dropZone.classList.remove("dragover"));
        this.dropZone.addEventListener("drop", e => {
            e.preventDefault(); this.dropZone.classList.remove("dragover");
            const file = e.dataTransfer.files[0]; if(file) this.handleFile(file);
        });

        // Paste
        document.addEventListener("paste", e => {
            const items = e.clipboardData.items;
            for(let i=0;i<items.length;i++){
                if(items[i].kind==="file"){
                    this.handleFile(items[i].getAsFile()); break;
                }
            }
        });

        // File selection
        this.fileInput.addEventListener("change", e => { const file = e.target.files[0]; if(file) this.handleFile(file); });

        // Continue button
        this.continueButton.addEventListener("click", () => {
            if(!this.selectedFile){ this.showError("Aucune image sélectionnée."); return; }
            if(!this.isValidUser){ this.showError("Vous devez être connecté et validé pour déposer une image."); return; }
            this.uploadFile();
        });
    }

    handleFile(file) {
        const allowedTypes=["image/jpeg","image/png","image/webp"];
        const maxSize=2*1024*1024;

        this.message.textContent=""; this.message.style.color="black";

        if(!allowedTypes.includes(file.type)){ this.showError("Type de fichier non supporté. Formats : JPG, PNG, WEBP."); return; }
        if(file.size>maxSize){ this.showError("Image trop volumineuse (>2 Mo)."); return; }

        const img = new Image();
        img.onload = () => {
            if(img.width<512 || img.height<512){ this.showError("Image trop petite (min 512x512)."); return; }

            this.selectedFile = file;
            this.message.textContent = "Image bien importée, cliquez sur Continuer.";
            this.message.style.color = "green";
            this.continueButton.style.display = "inline-block";

            // Remove previous content and append image to drop-zone
            this.dropZone.innerHTML = "";
            img.style.width = "100%";
            img.style.height = "100%";
            img.style.objectFit = "contain";
            img.style.borderRadius = "8px";
            this.dropZone.appendChild(img);
        };
        img.src = URL.createObjectURL(file);
    }


    showError(msg){
        this.message.textContent=msg;
        this.message.style.color="red";
        this.continueButton.style.display="none";
        this.preview.style.display="none";
        this.selectedFile=null;
    }

    uploadFile(){
        const formData=new FormData();
        formData.append("image_input", this.selectedFile);
        formData.append("upload", true);

        this.message.textContent="Téléversement en cours..."; this.message.style.color="orange";

        fetch("../control/images_control.php",{method:"POST",body:formData})
        .then(res=>res.json())
        .then(data=>{
            if(data.status==="success"){
                window.location.href="crop_images_views.php?img="+encodeURIComponent(data.file);
            }else this.showError(data.message);
        })
        .catch(err=>this.showError("Erreur : "+err));
    }
}

document.addEventListener("DOMContentLoaded",()=>{ new DragDropController(isValidUser); });
