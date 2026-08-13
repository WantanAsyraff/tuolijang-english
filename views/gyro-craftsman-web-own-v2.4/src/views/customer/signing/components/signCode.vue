import { $ } from '@/lang'
<template>
    <oa-dialog ref="oaDialog" :fromData="fromData" :visible.sync="visible" :isFooter="false" @handleClose="closeDialog" >
        <div class="sign-code-container">
           <div ref="qrcode" class="qrcode" />
            <div class="sign-info">
                <span class="scan-tip">{{ $("legacy.868a62f1855f981d") }}</span>
                <span @click="saveQrcode" class="save-btn">{{ $("legacy.850de13a01d9b482") }}</span>
            </div>
            <!-- <div class="pc-link-wrapper">
                <span class="link-label">PC端签约链接：</span>
                <a href="https://tuoluojiang.com" target="_blank" class="link-url">https://tuoluojiang.com</a>
                <el-button type="text" @click="copyLink" class="copy-btn">复制链接</el-button>
            </div> -->
        </div>
    </oa-dialog>
</template>

<script>
    import QRCode from 'qrcodejs2'
export default {
    name: 'SignCode',
    components: {
        oaDialog: () => import('@/components/form-common/dialog-form'),
        QRCode
    },
    data() {
        return {
            qrcodeUrl: 'https://tuoluojiang.com',
            linkUrl: 'https://tuoluojiang.com',
            fromData: {
                width: '500px',
                title: $('legacyScript.signerSigning'),
                btnText: '确定',
                labelWidth: '100px',
                type: 'slot'
            },
            visible: false,
            
        }
    },
    methods: {
        closeDialog() {
            this.$refs.qrcode.innerHTML = '';
         
        },
        openBox(rowData) {
            this.visible = true
            // 先清空二维码容器
               if (rowData.app_url) {
               setTimeout(() => {
                 
                new QRCode(this.$refs.qrcode, {
                  text: rowData.app_url,
                  width: 170,
                  height: 170,
                  colorDark: '#000000',
                  colorLight: '#ffffff',
                  correctLevel: QRCode.CorrectLevel.H
                });
              }, 300);
              
            }
               this.$refs.oaDialog.openBox();
        },
        // 保存二维码图片
        saveQrcode() {
            // 获取二维码 canvas
            const qrcodeCanvas = this.$refs.qrcode.querySelector('canvas');
            if (!qrcodeCanvas) {
                this.$message.error($('legacyScript.theQRCodeHasNotBeenGeneratedYetPleaseTry'));
                return;
            }

            // 将 canvas 转为 blob 并下载
            qrcodeCanvas.toBlob((blob) => {
                if (!blob) {
                    this.$message.error($('legacyScript.failedToGenerateImage'));
                    return;
                }
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = '签约二维码.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                this.$message.success($('legacyScript.qRCodeImageSaved'));
            });
        },

        // 复制链接
        copyLink() {
            // 使用Clipboard API复制链接
            navigator.clipboard.writeText(this.linkUrl).then(() => {
                this.$message.success($('legacyScript.linkCopiedToClipboard'));
            }).catch(err => {
                console.error($('legacyScript.copyFail'), err);
                this.$message.error($('legacyScript.copyFailedPleaseCopyManually'));
            });
        }
    }
}
</script>

<style lang="scss" scoped>
.sign-code-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 10px;
    margin-bottom: 37px;


    .qrcode{
         display: block;
            width: 170px;
            height: 170px;
        margin-bottom: 30px;

        
    }

    .sign-info {
        display: flex;
        align-items: center;
        justify-content: center;
        // margin-bottom: 20px;
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        margin-bottom: 10px;

        .scan-tip {

            font-size: 13px;
            color: #303133;
        }

        .save-btn {
            cursor: pointer;

            font-size: 13px;
            color: #1890FF;
            margin-left: 10px;
        }
    }

    .pc-link-wrapper {
        display: flex;
        align-items: center;
        font-size: 13px;

        .link-label {
            font-size: 13px;
            color: #303133;
            margin-right: 8px;
        }

        .link-url {
            color: #409EFF;
            margin-right: 8px;
            text-decoration: none;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;

            &:hover {
                text-decoration: underline;
            }
        }

        .copy-btn {
            color: #409EFF;
            padding: 0;
        }
    }
}
</style>