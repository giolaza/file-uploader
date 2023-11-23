<template>
    <div>
        <input type="file" @change="handleFileUpload"/>
        <progress :value="progress" max="100"></progress>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedFile: null,
            progress: 0
        };
    },
    methods: {
        async handleFileUpload(event) {
            this.selectedFile = event.target.files[0];
            const chunkSize = 1024 * 1024; // 1MB per chunk
            const totalChunks = Math.ceil(this.selectedFile.size / chunkSize);
            let start = 0;

            for (let chunkIndex = 0; start < this.selectedFile.size; chunkIndex++) {
                const chunk = this.selectedFile.slice(start, start + chunkSize);
                await this.uploadChunk(chunk, chunkIndex, totalChunks);
                start += chunkSize;
                this.progress = Math.min(100, (start / this.selectedFile.size) * 100);
            }
        },
        async uploadChunk(chunk, chunkIndex, totalChunks) {
            const formData = new FormData();
            formData.append('file', chunk);

            try {
                await axios.post('/file-upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-Original-Name': this.selectedFile.name,
                        'X-Chunk-Index': chunkIndex,
                        'X-Total-Chunks': totalChunks
                    },
                    timeout: 600000//10 min
                });
            } catch (error) {
                console.error('Upload failed:', error);
                await this.uploadChunk(chunk, chunkIndex, totalChunks); //here is infinite cycle, if need can be added retry limit
            }
        }
    }
};
</script>
