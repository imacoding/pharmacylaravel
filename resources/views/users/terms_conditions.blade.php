@extends('users.layouts.index_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')
<section class="default-page">
    <div class="container">
        <div class="default-page-header">
            <h1>Terms and Conditions</h1>
            <p>LAST MODIFICATION : JUNE 2021</p>
        </div>
        <p>
            This agreement is with Healwire
            </p></br/>
            <p>
                Welcome to Healwire. These are the terms and conditions that control your use of this site.
            </p>
            <br/>
        <div>
            <p>
                THESE TERMS AND CONDITIONS OF USE CONSTITUTE A LEGAL AGREEMENT BETWEEN YOU AND HEALWIRE. BEFORE YOU CONTINUE TO USE THIS WEBSITE OR DOWNLOAD ANY IMAGE, PLEASE READ THIS AGREEMENT ("AGREEMENT") IN ITS ENTIRETY 
            </p>
        </div>
        <div class="default-page-details">
        
            <h2>
            Ownership of this Website
            </h2>
            <p>
            This website is owned and operated by Healwire. All of the content featured or displayed on this website, including, but not limited to, text, graphics, photographs, moving images, sound, illustrations and software ("Content"), is owned by Healwire, its licensors and its content providers.
            </p></br>
            <p>
            All elements of the Healwire website and mobile application including, but not limited to, the general design and the Content, are protected by trade dress, copyright, moral rights, trademark and other laws relating to intellectual property rights. Except as explicitly permitted under this or another agreement with Healwire or one of its subsidiaries, no portion or element of this website or its Content may be copied or retransmitted via any means and this website, its Content and all related rights shall remain the exclusive property of Healwire or its licensors unless otherwise expressly agreed. You shall indemnify Healwire, its subsidiaries, its affiliates and licensors against any losses, expenses, costs or damages incurred by any or all of them as a result of your breach of the terms of this Agreement or unauthorized use of the Content and related rights.
 
            </p>
        </div>
        <div class="default-page-details">
        
            <h2>
            Disclaimers
            </h2>
            <p>
            THIS WEBSITE AND ITS CONTENT ARE PROVIDED "AS IS" AND HEALWIRE EXCLUDES TO THE FULLEST EXTENT PERMITTED BY APPLICABLE LAW ANY WARRANTY, EXPRESS OR IMPLIED, INCLUDING, WITHOUT LIMITATION, ANY IMPLIED WARRANTIES OF MERCHANTABILITY, SATISFACTORY QUALITY OR FITNESS FOR A PARTICULAR PURPOSE. HEALWIRE WILL NOT BE LIABLE FOR ANY DAMAGES OF ANY KIND ARISING FROM THE USE OF THIS WEBSITE, INCLUDING, BUT NOT LIMITED TO DIRECT, INDIRECT, INCIDENTAL, PUNITIVE AND CONSEQUENTIAL DAMAGES. THE FUNCTIONS EMBODIED ON, OR IN THE MATERIALS OF, THIS WEBSITE ARE NOT WARRANTED TO BE UNINTERRUPTED OR WITHOUT ERROR. YOU, NOT HEALWIRE, ASSUME THE ENTIRE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION DUE TO YOUR USE OF THIS WEBSITE.
            </p></br>
            <p>
            Except as specifically stated in these Terms and Conditions of Use or elsewhere on this website, or as otherwise required by applicable law, neither Healwire nor its directors, employees, licensors, content providers, affiliates or other representatives will be liable for damages of any kind (including, without limitation, lost profits, direct, indirect, compensatory, consequential, exemplary, special, incidental, or punitive damages) arising out of your use of, your inability to use, or the performance of this website or the Content whether or not we have been advised of the possibility of such damages. 
            </p><br/>
            <p>
            Healwire uses reasonable efforts to ensure the accuracy, correctness and reliability of the Content, but we make no representations or warranties as to the Content's accuracy, correctness or reliability.

            </p><br/>
            <p>There may be links to other websites from the Healwire website; however, these other websites are not controlled by Healwire and we are not responsible for any content contained on any such website or any loss suffered by you in relation to your use of such websites. You waive any and all claims against Healwire regarding the inclusion of links to outside websites or your use of those websites. Additionally, parties other than Healwire provideprovides services from this website. For example, you may obtain information regarding certain services through Healwire. Healwire does not evaluate or warrant the offerings or services of these parties and does not assume any liability for the actions, product, services, or content of these parties.
            </p><br/>
            <p>Some countries do not permit the exclusion or limitation of implied warranties or liability for certain categories of damages. Therefore, some or all of the limitations above may not apply to you to the extent they are prohibited or superseded by state or national provisions.</p>
        </div>
        <div class="default-page-details">
        
            <h2>
            Governing Law and Venue.
            </h2>
            <p>
            This Agreement shall be interpreted, construed and governed by the laws of India.
            </p></br>
            <p>
            No Waiver, Severability

            </p><br/>
            <p>
            No action of Healwire, other than an express written waiver or amendment, may be construed as a waiver or amendment of any of these Terms and Conditions of Use. Should any clause of these Terms and Conditions of Use be found unenforceable, wherever possible this will not affect any other clause and each will remain in full force and effect.

            </p><br/>
            <p>We reserve the right to change these Terms and Conditions of Use, prices, information and available contractual license terms featured on this website without notice. These conditions set out the entire agreement between Healwire and you relating to your use of this website.
            </p><br/>
            <p>Some countries do not permit the exclusion or limitation of implied warranties or liability for certain categories of damages. Therefore, some or all of the limitations above may not apply to you to the extent they are prohibited or superseded by state or national provisions.</p>
        </div>
        
        <div class="default-page-details">
            <h2>
            Comments
            </h2>
            <p>
            If you have any comments or questions about the Site please contact us at <a href="mailto:{{contact_mail()}}">{{contact_mail()}}</a>
            </p>
        </div>
    </div>
</section>

@include('users.layouts.index_modals')
</body>
@endsection
@push('js')
<script src="{{url('assets/js/common/homepage.js')}}" type="text/javascript"></script>
@endpush